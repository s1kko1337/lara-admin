<?php

namespace App\Listeners;

use App\Events\ChatUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendChatUpdateNotification
{
    public function __construct()
    {
        //
    }

    public function handle(ChatUpdated $event)
    {
        // Логика обработки события или отправки уведомления.
    }
}
