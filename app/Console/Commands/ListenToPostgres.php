<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use PDO;

class ListenToPostgres extends Command
{
    protected $signature = 'listen:postgres';
    protected $description = 'Listen for PostgreSQL notifications';

    public function handle()
    {
        // Открываем соединение с PostgreSQL
        $connection = DB::connection()->getPdo();

        // Подписка на канал уведомлений
        $connection->exec("LISTEN chat_update");

        // Прослушиваем уведомления
        while (true) {
            // Проверяем уведомления с таймаутом
            $connection->pgsqlGetNotify(PDO::FETCH_ASSOC, 100000);

            while ($notification = $connection->pgsqlGetNotify(PDO::FETCH_ASSOC)) {
                // Извлекаем данные из уведомления
                $chatId = $notification['payload'];
                event(new ChatUpdated($chatId));
            }

        }
    }
}
