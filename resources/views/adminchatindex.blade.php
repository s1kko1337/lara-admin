@extends('layout')

@section('content')
    <h1>Список чатов</h1>
    <div class="row">
        @foreach ($chats as $chat)
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $chat->chat_article }}</h5>
                        <p class="card-text">Chat ID: {{ $chat->chat_id }}</p>
                        <a href="{{ route('user.chats.show', $chat->chat_id) }}" class="btn btn-primary">Перейти к чату</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
