<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Work;
use PDF;

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
            'model_name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'file' => 'required|file|max:4096',
            'preview' => 'required|image|max:2048',
        ]);
    
        $file = $request->file('file');
        $fileContent = file_get_contents($file->getRealPath());
        $fileContentBase64 = base64_encode($fileContent);
    
    
        $preview = $request->file('preview');
        $previewContent = file_get_contents($preview->getRealPath());
        $previewContentBase64 = base64_encode($previewContent);
    
        $localPath = $file->store('uploads'); 
    
        if (!Storage::exists($localPath)) {
            return redirect()->back()->with('error', 'Ошибка сохранения файла на сервере.');
        }
    
        $ftpDisk = Storage::disk('ftp'); 
        $remotePath = 'upload/' . $file->getClientOriginalName();
    
        try {
            if ($ftpDisk->put($remotePath, fopen(storage_path('app/' . $localPath), 'r+'))) {
            
            
                Work::create([
                    'modeler_id' => Auth::id(),
                    'model_name' => $request->input('model_name'),
                    'path_to_model' => $remotePath,
                    'additional_info' => $request->input('description'),
                    'binary_file' => $fileContentBase64,
                    'binary_preview' => $previewContentBase64,
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
    
        public function listModels(Request $request)
    {
        $query = Work::where('modeler_id', Auth::id());
    
        if ($request->filled('search_id')) {
            $query->where('id', $request->input('search_id'));
        }
    
        if ($request->filled('search_name')) {
            $query->where('model_name', 'like', '%' . $request->input('search_name') . '%');
        }
    
        $models = $query->get();
    
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
            \Log::info('Путь к файлу для удаления: ' . $remotePath);

            if ($ftpDisk->exists($remotePath)) {
                if ($ftpDisk->delete($remotePath)) {
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
            'model_name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'file' => 'nullable|file|max:4096',
            'preview' => 'nullable|image|max:2048',
        ]);
    
        $model->model_name = $request->input('model_name');
        $model->additional_info = $request->input('description');
    
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileContent = file_get_contents($file->getRealPath());
            $fileContentBase64 = base64_encode($fileContent);
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
                    $model->binary_file = $fileContentBase64;
                } else {
                    return redirect()->back()->with('error', 'Не удалось загрузить файл на FTP.');
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Ошибка загрузки файла: ' . $e->getMessage());
            }
        }
    
        if ($request->hasFile('preview')) {
            $preview = $request->file('preview');
            $previewContent = file_get_contents($preview->getRealPath());
            $previewContentBase64 = base64_encode($previewContent);
            $model->binary_preview = $previewContentBase64;
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

        return view('editModel', compact('model'));
    }

        public function downloadCsv()
    {
        try {
            $models = DB::table('works')->where('modeler_id', Auth::id())->get();

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="models.csv"',
            ];

            $callback = function() use ($models) {
                $file = fopen('php://output', 'w');

                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

                fputcsv($file, ['ID', 'Название модели', 'Описание', 'Путь к модели'], ';');

                foreach ($models as $model) {
                    fputcsv($file, [
                        $model->id,
                        $model->model_name,
                        $model->additional_info,
                        $model->path_to_model
                    ], ';');
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            \Log::error('Ошибка генерации CSV: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ошибка при генерации CSV файла.');
        }
    }

    public function exportPdf()
    {
        try {
            $userId = Auth::id();

            $models = Work::where('modeler_id', $userId)->get();

            $data = [
                'filesCount' => $models->count(),
                'files' => $models->map(function($model) {
                    return [
                        'name' => $model->model_name,
                        'uploaded_at' => $model->created_at->format('d.m.Y H:i'),
                    ];
                }),
                'models' => $models,
            ];

            $pdf = Pdf::loadView('pdf.models', $data);

            return $pdf->download('models.pdf');
        } catch (\Exception $e) {
            \Log::error('Ошибка генерации PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ошибка при генерации PDF-файла.');
        }
    }
    public function checkModels(Request $request)
    {
        try {
            $request->validate([
                'csv_file' => 'required|file|mimes:csv,txt',
            ]);
    
            $csvFile = $request->file('csv_file');
    
            if (!$csvFile->isValid()) {
                return redirect()->back()->with('error', 'Ошибка загрузки CSV файла.');
            }
    
            $path = $csvFile->getRealPath();
    
            $modelsFromCsv = [];
    
            if (($handle = fopen($path, 'r')) !== false) {
                $header = fgetcsv($handle, 1000, ';');
    
                // Remove BOM from the first header field, if present
                if ($header && count($header) > 0) {
                    $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
                }
    
                $headerMapping = array_flip($header);
    
                while (($data = fgetcsv($handle, 1000, ';')) !== false) {
                    $modelsFromCsv[] = [
                        'id' => $data[$headerMapping['ID']],
                        'model_name' => $data[$headerMapping['Название модели']],
                        'additional_info' => $data[$headerMapping['Описание']],
                        'path_to_model' => $data[$headerMapping['Путь к модели']],
                    ];
                }
                fclose($handle);
            }
    
    
            $results = [];
    
            foreach ($modelsFromCsv as $modelData) {
                $modelExistsInDb = Work::where('path_to_model', $modelData['path_to_model'])
                    ->where('modeler_id', Auth::id())
                    ->exists();
    
                $modelFileExistsOnFtp = false;
    
                $remotePath = trim($modelData['path_to_model']);
    
                $ftpDisk = Storage::disk('ftp');
    
                try {
                    if ($ftpDisk->exists($remotePath)) {
                        $modelFileExistsOnFtp = true;
                    }
                } catch (\Exception $e) {
                    \Log::error('Ошибка доступа к FTP для файла ' . $remotePath . ': ' . $e->getMessage());
                }
    
                $results[] = [
                    'id' => $modelData['id'],
                    'model_name' => $modelData['model_name'],
                    'exists_in_db' => $modelExistsInDb,
                    'exists_on_ftp' => $modelFileExistsOnFtp,
                ];
            }
    
            return view('checkmodelsresult', compact('results'));
    
        } catch (\Exception $e) {
            \Log::error('Ошибка проверки наличия моделей на сервере: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ошибка при проверке наличия моделей.');
        }
    }

    
}
