<?php

namespace App\Http\Controllers;

use App\Events\ChatUpdated;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // Метод для отображения списка чатов
    public function index()
    {
        $chats = DB::table('messages_stat')
            ->select('chat_id', 'chat_article', 'chat_status')
            ->whereNotNull('chat_article')->where('id_user',  auth()->id())
            ->groupBy('chat_id', 'chat_article', 'chat_status')
            ->get();
    
        return view('adminchatindex', compact('chats'));
    }

    public function show($chat_id)
    {
        $messages = Message::where('chat_id', $chat_id)->where('id_user',  auth()->id())->orderBy('id')->get();
        // foreach ($messages as $message) {
        //     $message->created_at = $message->created_at->setTimezone('Europe/Moscow')->format('d.m.Y, H:i:s');
        // }

        return view('adminchatsshow', compact('messages'));
    }

    public function activate($chat_id)
    {
        DB::table('messages_stat')
            ->where('chat_id', $chat_id)
            ->update(['chat_status' => 'active']);

        return redirect()->back();
    }

    public function deactivate($chat_id)
    {
        DB::table('messages_stat')
            ->where('chat_id', $chat_id)
            ->update(['chat_status' => 'inactive']);

        return redirect()->back();
    }

    public function getUpdatedChats()
    {
        $chats = DB::table('messages_stat')
            ->select('chat_id', 'chat_article', 'chat_status')
            ->whereNotNull('chat_article')->where('id_user',  auth()->id())
            ->groupBy('chat_id', 'chat_article', 'chat_status')
            ->get();
        
        return response()->json($chats);
    }
    
    public function sendMessage(Request $request, $chat_id)
    {
        $validated = $request->validate([
            'message_text' => 'required|string',
        ]);

        $id = DB::table('messages_stat')
        ->max('id')+1;

        Message::create([
            'created_at' => now(),
            'id_user' => auth()->id(),
            'is_admin' => 'true',
            'id' => $id, 
            'chat_id' => $chat_id,
            'chat_status' => 'active',
            'message_text' => $validated['message_text'],
        ]);

        return redirect()->route('user.chats.show', $chat_id);
    }

public function getMessages($chatId)
{
    $messages = Message::where('chat_id', $chatId)->where('id_user',  auth()->id())->latest()->get();

    // foreach ($messages as $message) {
    //     $message->created_at = $message->created_at->setTimezone('Europe/Moscow')->format('d.m.Y, H:i:s');
    // }

    return response()->json($messages);
}


public function deleteChat($inputChatId)
{
    if (empty($inputChatId)) {
        return redirect()->route('user.chats.index')->with('error', 'Чат не найден.');
    }
    $chatId = filter_var($inputChatId, FILTER_SANITIZE_NUMBER_INT);
    try {
        // Удаляем все записи с указанным chat_id из таблицы messages_stat
        DB::table('messages_stat')->where('chat_id', $chatId)->where('id_user',  auth()->id())->delete();

        return redirect()->route('user.chats.index')->with('success', 'Чат успешно удален.');
    } catch (\Exception $e) {
        \Log::error('Ошибка удаления чата: ' . $e->getMessage());
        return redirect()->route('user.chats.index')->with('error', 'Ошибка удаления чата: ' . $e->getMessage());
    }
}
// Метод для получения новых сообщений
public function getNewMessages(Request $request, $chat_id)
{
    $lastMessageId = $request->query('last_message_id');

    $newMessages = Message::where('chat_id', $chat_id)
        ->where('id', '>', $lastMessageId)
        ->orderBy('created_at', 'asc')
        ->get();

    // foreach ($newMessages as $message) {
    //     $message->created_at = $message->created_at->setTimezone('Europe/Moscow')->format('d.m.Y, H:i:s');
    // }

    return response()->json($newMessages);
}


}
