@extends('layout')

@section('content')
    <h1 style="margin-top: 10px;">Тема обращения: {{ $messages->first()->chat_article }}</h1>
    <div id="message-container" class="card shadow-lg p-4 rounded" style="border-radius: 15px; margin-top: 10px; margin-bottom: 10px; width: 85%; max-width: 900px; margin-left: auto; margin-right: auto;">
        <ul id="message-list" class="list-group mb-3">
            @foreach ($messages as $message)
                <li class="list-group-item d-flex justify-content-{{ $message->is_admin ? 'end' : 'start' }}">
                      @if ( $message->message_text !== 'nullablefirstmessageforopenchat')
                      <div class="d-flex flex-column">
                        <div class="p-2" style="border-radius: 10px; background-color: {{ $message->is_admin ? '#d4edda' : '#f8d7da' }}; max-width: 70%;">
                            <p class="mb-0">{{ $message->message_text }}</p>
                        </div>
                        <small class="text-muted text-end">{{ $message->created_at }}</small>
                    </div>
                    @endif
                </li>
            @endforeach
        </ul>

        <form action="{{ route('user.chats.send', [$messages->first()->chat_id, $messages->last()->id]) }}" method="POST">
            @csrf
            <div class="input-group mb-3">
                <textarea name="message_text" class="form-control rounded" required></textarea>
                <button type="submit" class="btn btn-success rounded">Отправить</button>
            </div>
        </form>
    </div>
@endsection
@section('scripts')
<script>
 document.addEventListener('DOMContentLoaded', function () {
    const chatId = {{ $messages->first()->chat_id }};
    const messageContainer = document.getElementById('message-list');
    let lastMessageId = {{ $messages->last()->id }};
    const loadedMessageIds = new Set();

    document.querySelectorAll('#message-list li').forEach((li) => {
        const messageId = li.dataset.messageId;
        if (messageId) {
            loadedMessageIds.add(parseInt(messageId));
        }
    });

    async function fetchNewMessages() {
        try {
            const response = await fetch(`{{ route('user.chats.getNewMessages', ':chatId') }}`.replace(':chatId', chatId) + `?last_message_id=${lastMessageId}`);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const newMessages = await response.json();

            newMessages.forEach(message => {
                if (!loadedMessageIds.has(message.id)) {
                    const createdAt = new Date(message.created_at);
                    const formattedDate = `${createdAt.getFullYear()}-${String(createdAt.getMonth() + 1).padStart(2, '0')}-${String(createdAt.getDate()).padStart(2, '0')} ${String(createdAt.getHours()).padStart(2, '0')}:${String(createdAt.getMinutes()).padStart(2, '0')}:${String(createdAt.getSeconds()).padStart(2, '0')}`;

                    const messageHtml = `
                        <li class="list-group-item d-flex justify-content-${message.is_admin ? 'end' : 'start'}" data-message-id="${message.id}">
                            <div class="d-flex flex-column">
                                <div class="p-2" style="border-radius: 10px; background-color: ${message.is_admin ? '#d4edda' : '#f8d7da'}; max-width: 70%;">
                                    <p class="mb-0">${message.message_text}</p>
                                </div>
                                <small class="text-muted text-end">${formattedDate}</small>
                            </div>
                        </li>
                    `;
                    messageContainer.insertAdjacentHTML('beforeend', messageHtml);

                    loadedMessageIds.add(message.id);
                    lastMessageId = message.id;
                }
            });
        } catch (error) {
            console.error('Error fetching new messages:', error);
        }
    }

    setInterval(fetchNewMessages, 500);
});

</script>
@endsection
