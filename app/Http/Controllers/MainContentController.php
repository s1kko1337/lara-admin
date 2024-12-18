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
        $uniqueIdCount = DB::table('messages_stat')->where('id_user',  auth()->id())
        ->distinct()
        ->count('chat_id');
        $uniqueIdCountActive = DB::table('messages_stat')
        ->distinct()->where('id_user',  auth()->id())
        ->where('chat_status', 'active') 
        ->count('chat_id');
    
    $uniqueIdCountInactive = DB::table('messages_stat')
        ->distinct()->where('id_user',  auth()->id())
        ->where('chat_status', 'inactive') 
        ->count('chat_id');
    
        $res = [$uniqueIdCount,$uniqueIdCountActive,$uniqueIdCountInactive];
        return $res;
    }

    public function getStats(Request $request)
    {
        $timeRange = $request->input('timeRange', 'all'); 
        $statType = $request->input('statType', 'chats'); 
    
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
    
        if ($statType === 'chats') {
            $query = DB::table('messages_stat')->where('id_user', auth()->id());
    
            if ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }
    
            $stats = $query->select('chat_id', DB::raw('COUNT(*) as message_count'))
                ->groupBy('chat_id')
                ->get();
        } elseif ($statType === 'models') {
            $query = DB::table('works')->where('modeler_id', auth()->id());
    
            if ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }
    
            $stats = $query->select('model_name', 'views')
                ->get();

        } elseif ($statType === 'profile') {
            $totalViews = DB::table('portfolios')
            ->where('id', auth()->id())
            ->select('views')->get();

            $stats = [
 'value' => $totalViews
            ];
        } else {
            $stats = [];
        }
    
        return response()->json($stats);
    }
}
