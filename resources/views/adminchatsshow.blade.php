@extends('layout')

@section('content')
    <h1 style="margin-top: 10px;">Тема обращения: {{ $messages->first()->chat_article }}</h1>
    <div id="message-container" class="card shadow-lg p-4 rounded" style="border-radius: 15px; margin-top: 10px; margin-bottom: 10px; width: 85%; max-width: 900px; margin-left: auto; margin-right: auto;">
        <ul id="message-list" class="list-group mb-3">
            @foreach ($messages as $message)
                <li class="list-group-item d-flex justify-content-{{ $message->is_admin ? 'end' : 'start' }}">
                    <div class="d-flex flex-column">
                        <div class="p-2" style="border-radius: 10px; background-color: {{ $message->is_admin ? '#d4edda' : '#f8d7da' }}; max-width: 70%;">
                            <p class="mb-0">{{ $message->message_text }}</p>
                        </div>
                        <small class="text-muted text-end">{{ $message->created_at }}</small>
                    </div>
                </li>
            @endforeach
        </ul>

        <form action="{{ route('user.chats.send', $messages->first()->chat_id) }}" method="POST">
            @csrf
            <div class="input-group mb-3">
                <textarea name="message_text" class="form-control rounded" required></textarea>
                <button type="submit" class="btn btn-success rounded">Отправить</button>
            </div>
        </form>
    </div>
@endsection
@section('scripts')
    @vite('resources/js/app.js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chatId = {{ $messages->first()->chat_id }};
            
            if (window.Echo) {
                window.Echo.channel('chat.' + chatId)
                    .listen('ChatUpdated', (e) => {
                        const messageHtml = `
                            <li class="list-group-item d-flex justify-content-${e.is_admin ? 'end' : 'start'}">
                                <div class="d-flex flex-column">
                                    <div class="p-2" style="border-radius: 10px; background-color: ${e.is_admin ? '#d4edda' : '#f8d7da'}; max-width: 70%;">
                                        <p class="mb-0">${e.message_text}</p>
                                    </div>
                                    <small class="text-muted text-end">${e.created_at}</small>
                                </div>
                            </li>
                        `;
                        document.getElementById('message-list').insertAdjacentHTML('beforeend', messageHtml);
                        location.reload();
                    });
            } else {
                console.error('Echo is not defined');
            }
        });
    </script>
@endsection
