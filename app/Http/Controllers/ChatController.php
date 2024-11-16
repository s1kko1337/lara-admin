<?php

namespace App\Http\Controllers;

use App\Events\ChatUpdated;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    // Метод для отображения списка чатов
    public function index()
    {
        $chats = DB::table('messages_stat')
        ->select('chat_id', 'chat_article')
        ->whereNotNull('chat_article')
        ->groupBy('chat_id', 'chat_article')
        ->get();

        
        return view('adminchatindex', compact('chats'));
    }

    // Метод для отображения конкретного чата
// Метод для отображения конкретного чата
public function show($chat_id)
{
    $messages = Message::where('chat_id', $chat_id)->orderBy('created_at')->get();
    foreach ($messages as $message) {
        $message->created_at = $message->created_at->setTimezone('Europe/Moscow')->format('d.m.Y, H:i:s');
    }

    return view('adminchatsshow', compact('messages'));
}

    // Метод для отправки сообщения от админа
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
// Метод для получения сообщений в нужном формате
public function getMessages($chatId)
{
    $messages = Message::where('chat_id', $chatId)->latest()->get();

    foreach ($messages as $message) {
        $message->created_at = $message->created_at->setTimezone('Europe/Moscow')->format('d.m.Y, H:i:s');
    }

    return response()->json($messages);
}

// Метод для обновления сообщения
public function updateMessage(Request $request, $id)
{
    $validated = $request->validate([
        'message_text' => 'required|string',
    ]);

    $message = Message::findOrFail($id);
    $message->message_text = $validated['message_text'];
    $message->save();

    return redirect()->back()->with('success', 'Сообщение обновлено!');
}

// Метод для удаления сообщения
public function deleteMessage($id)
{
    $message = Message::findOrFail($id);
    $message->delete();

    return redirect()->back()->with('success', 'Сообщение удалено!');
}
// Метод для получения новых сообщений
public function getNewMessages(Request $request, $chat_id)
{
    $lastMessageId = $request->query('last_message_id');

    $newMessages = Message::where('chat_id', $chat_id)
        ->where('id', '>', $lastMessageId)
        ->orderBy('created_at', 'asc')
        ->get();

    foreach ($newMessages as $message) {
        $message->created_at = $message->created_at->setTimezone('Europe/Moscow')->format('d.m.Y, H:i:s');
    }

    return response()->json($newMessages);
}


}
