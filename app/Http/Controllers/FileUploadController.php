<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    public function upload(Request $request)
    {
        // Валидация входящих данных
        $request->validate([
            'file' => 'required|file|max:2048', // Максимальный размер файла 2MB
            'description' => 'required|string|max:255',
        ]);

        $file = $request->file('file');
        $localPath = $file->store('uploads');

        $ftpDisk = Storage::disk('ftp');
        $remotePath = 'upload/' . $file->getClientOriginalName(); 

        if ($ftpDisk->put($remotePath, fopen(storage_path('app/' . $localPath), 'r+'))) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'error' => 'Не удалось загрузить файл на FTP.']);
        }
    }
}
