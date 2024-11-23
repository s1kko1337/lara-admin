<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class MainContentController extends Controller
{
    public function showHome(Request $request) {
        $ftpAvailable = $this->checkFtpServer();
        $user = Auth::user();
        $models = DB::table('works')->where('modeler_id', Auth::id())->get();
        $uniqueIdCount = $this->countChats()[0];
        $uniqueIdCountActive = $this->countChats()[1];
        $uniqueIdCountInactive = $this->countChats()[2];
        return view('home',  compact('ftpAvailable', 'models', 'uniqueIdCount','uniqueIdCountActive','uniqueIdCountInactive'));
    }

    private function checkFtpServer()
    {
        try {
            $ftpDisk = Storage::disk('ftp');
           // $files = $ftpDisk->files('/upload');
            //return response()->json(['success' => true, 'files' => $files]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }


    private function countChats()
    {
        $uniqueIdCount = DB::table('messages_stat')
        ->distinct()
        ->count('chat_id');
        $uniqueIdCountActive = DB::table('messages_stat')
        ->distinct()
        ->where('chat_status', 'active') // Исправлено на 'chat_status'
        ->count('chat_id');
    
    $uniqueIdCountInactive = DB::table('messages_stat')
        ->distinct()
        ->where('chat_status', 'inactive') // Исправлено на 'chat_status'
        ->count('chat_id');
    
        $res = [$uniqueIdCount,$uniqueIdCountActive,$uniqueIdCountInactive];
        return $res;
    }

    // app/Http/Controllers/MainContentController.php

public function getChatStats(Request $request)
{
    $timeRange = $request->input('timeRange', 'all'); // Default to 'all' if not provided

    switch ($timeRange) {
        case 'day':
            $startDate = now()->startOfDay();
            break;
        case 'week':
            $startDate = now()->startOfWeek();
            break;
        case 'month':
            $startDate = now()->startOfMonth();
            break;
        default:
            $startDate = null; 
            break;
    }

    $query = DB::table('messages_stat');

    if ($startDate) {
        $query->where('created_at', '>=', $startDate);
    }

    $chatStats = $query->select('chat_id', DB::raw('COUNT(*) as message_count'))
        ->groupBy('chat_id')
        ->get();

    return response()->json($chatStats);
}
}
