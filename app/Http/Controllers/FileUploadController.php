<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Work;

class FileUploadController extends Controller
{
    public function checkFtpConnection()
    {
        try {
            $ftpDisk = Storage::disk('ftp');
            $files = $ftpDisk->files('/upload');
            return response()->json(['success' => true, 'files' => $files]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function get()
    {
        $models = DB::table('works')->where('modeler_id', Auth::id())->get();
        return view('upload-file', compact('models'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'model_name' => 'required|string|max:255', // Добавлено
            'description' => 'required|string|max:255',
            'file' => 'required|file|max:4096',
        ]);
    
        $file = $request->file('file');
        $localPath = $file->store('uploads'); 
    
        if (!Storage::exists($localPath)) {
            return redirect()->back()->with('error', 'Ошибка сохранения файла на сервере.');
        }
    
        $ftpDisk = Storage::disk('ftp'); 
        $remotePath = 'upload/' . $file->getClientOriginalName(); 
    
        try {
            if ($ftpDisk->put($remotePath, fopen(storage_path('app/' . $localPath), 'r+'))) {
                DB::table('works')->insert([
                    'modeler_id' => Auth::id(),
                    'model_name' => $request->input('model_name'), // Добавлено
                    'path_to_model' => $remotePath,
                    'additional_info' => $request->input('description'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
    
                return redirect()->back()->with('success', 'Файл успешно загружен.');
            } else {
                return redirect()->back()->with('error', 'Не удалось загрузить файл на FTP.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ошибка загрузки файла: ' . $e->getMessage());
        }
    }

    public function downloadModel($id)
    {
        $model = Work::find($id);
    
        if (!$model || $model->modeler_id != Auth::id()) {
            return redirect()->back()->with('error', 'Модель не найдена или доступ запрещен.');
        }
    
        $ftpDisk = Storage::disk('ftp');
        $remotePath = trim($model->path_to_model);
    
        try {
            if ($ftpDisk->exists($remotePath)) {
                $stream = $ftpDisk->readStream($remotePath);
                $fileName = basename($remotePath);
    
                return response()->stream(function() use ($stream) {
                    fpassthru($stream);
                }, 200, [
                    'Content-Type' => $ftpDisk->mimeType($remotePath),
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                ]);
            } else {
                return redirect()->back()->with('error', 'Файл не найден на FTP сервере.');
            }
        } catch (\Exception $e) {
            \Log::error('Ошибка скачивания файла: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ошибка скачивания файла: ' . $e->getMessage());
        }
    }
    
public function listModels()
{
    $models = DB::table('works')->where('modeler_id', Auth::id())->get();
    return view('upload-file', compact('models'));
}

public function deleteModel($id)
{
    $model = DB::table('works')->where('id', $id)->first();

    if (!$model) {
        return redirect()->back()->with('error', 'Модель не найдена.');
    }

    $ftpDisk = Storage::disk('ftp');
    $remotePath = trim($model->path_to_model);

    try {
        // Выводим путь в лог для отладки
        \Log::info('Путь к файлу для удаления: ' . $remotePath);

        // Удаление файла на FTP сервере
        if ($ftpDisk->exists($remotePath)) {
            if ($ftpDisk->delete($remotePath)) {
                // Удаление записи из базы данных
                DB::table('works')->where('id', $id)->delete();
                return redirect()->back()->with('success', 'Модель успешно удалена.');
            } else {
                return redirect()->back()->with('error', 'Не удалось удалить файл с FTP сервера.');
            }
        } else {
            return redirect()->back()->with('error', 'Файл не найден на FTP сервере.');
        }
    } catch (\Exception $e) {
        \Log::error('Ошибка удаления файла: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Ошибка удаления модели: ' . $e->getMessage());
    }
}

public function updateModel(Request $request, $id)
{
    $model = Work::find($id);

    if (!$model) {
        return redirect()->back()->with('error', 'Модель не найдена.');
    }

    $request->validate([
        'model_name' => 'required|string|max:255', // Добавлено
        'description' => 'required|string|max:255',
        'file' => 'nullable|file|max:2048',
    ]);

    $model->model_name = $request->input('model_name'); // Добавлено
    $model->additional_info = $request->input('description');

    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $localPath = $file->store('uploads');

        if (!Storage::exists($localPath)) {
            return redirect()->back()->with('error', 'Ошибка сохранения файла на сервере.');
        }

        $ftpDisk = Storage::disk('ftp');
        $remotePath = 'upload/' . $file->getClientOriginalName();

        try {
            if ($ftpDisk->put($remotePath, fopen(storage_path('app/' . $localPath), 'r+'))) {
                $oldRemotePath = trim($model->path_to_model);
                if ($ftpDisk->exists($oldRemotePath)) {
                    $ftpDisk->delete($oldRemotePath);
                }

                $model->path_to_model = $remotePath;
            } else {
                return redirect()->back()->with('error', 'Не удалось загрузить файл на FTP.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ошибка загрузки файла: ' . $e->getMessage());
        }
    }

    $model->save();

    return redirect()->route('user.file.get')->with('success', 'Модель успешно обновлена.');
}

public function editModel($id)
{
    $model = DB::table('works')->where('id', $id)->first();

    if (!$model) {
        return redirect()->back()->with('error', 'Модель не найдена.');
    }

    // Получаем все доступные теги

    return view('editModel', compact('model'));
}

}
