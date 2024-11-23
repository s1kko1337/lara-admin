@extends('layout')

@section('content')
    <h1 class="text-center">Список чатов
    <a href="{{ route('user.chats.index') }}" class="float-button btn btn-primary">
        <i class="fas fa-sync-alt"></i> Обновить список чатов
    </a>
    </h1>
    <div class="card shadow-lg p-4 rounded" style="border-radius: 15px; max-width:75%; margin-top: 10px; margin-bottom: 10px; margin-left: auto; margin-right: auto;">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 border-right">
                    <h3>Активные чаты</h3>
                    @foreach ($chats as $chat)
                        @if ($chat->chat_status == 'active')
                            <div class="mb-3">
                                <h5>{{ $chat->chat_article }}</h5>
                                <p>Chat ID: {{ $chat->chat_id }}</p>
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton{{ $chat->chat_id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Действия
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $chat->chat_id }}">
                                        <a class="dropdown-item" href="{{ route('user.chats.show', $chat->chat_id) }}">Перейти к чату</a>
                                        <form action="{{ route('user.chats.deactivate', $chat->chat_id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="dropdown-item">Сделать неактивным</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="col-md-6">
                    <h3>Неактивные чаты</h3>
                    @foreach ($chats as $chat)
                        @if ($chat->chat_status == 'inactive')
                            <div class="mb-3">
                                <h5>{{ $chat->chat_article }}</h5>
                                <p>Chat ID: {{ $chat->chat_id }}</p>
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton{{ $chat->chat_id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Действия
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $chat->chat_id }}">
                                        <a class="dropdown-item" href="{{ route('user.chats.show', $chat->chat_id) }}">Перейти к чату</a>
                                        <form action="{{ route('user.chats.activate', $chat->chat_id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="dropdown-item">Сделать активным</button>
                                        </form>
                                        <form method="POST" action="{{ route('user.chats.delete', $chat->chat_id) }}" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item">Удалить</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>


@endsection