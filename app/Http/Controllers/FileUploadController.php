<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        // Валидация входящих данных
        $request->validate([
            'file' => 'required|file|max:2048',
            'description' => 'required|string|max:255',
        ]);

        $file = $request->file('file');
        $localPath = $file->store('uploads'); // Загружаем файл на локальный диск

        if (!Storage::exists($localPath)) {
            return redirect()->back()->with('error', 'Ошибка сохранения файла на сервере.');
        }

        $ftpDisk = Storage::disk('ftp'); // Диск для работы с FTP
        $remotePath = 'upload/' . $file->getClientOriginalName(); // Путь на FTP сервере

        try {
            if ($ftpDisk->put($remotePath, fopen(storage_path('app/' . $localPath), 'r+'))) {
                // Сохраняем запись о файле в базе данных
                DB::table('works')->insert([
                    'modeler_id' => Auth::id(),
                    'path_to_model' => $remotePath,
                    'additional_info' => $request->description,
                ]);

                return redirect()->back()->with('success', 'Файл успешно загружен.');
            } else {
                return redirect()->back()->with('error', 'Не удалось загрузить файл на FTP.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ошибка загрузки файла: ' . $e->getMessage());
        }
    }


    // FileUploadController.php
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

public function updateModel($id)
{
    $model = DB::table('works')->where('id', $id)->first();

    if (!$model) {
        return redirect()->back()->with('error', 'Модель не найдена.');
    }

    $ftpDisk = Storage::disk('ftp');
    $remotePath = trim($model->path_to_model);

}

}
