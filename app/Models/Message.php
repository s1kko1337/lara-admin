<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages_stat';

    protected $fillable = [
        'created_at',
        'id_user',
        'updated_at',
        'chat_id',
        'is_admin',
        'chat_status',
        'message_text',
    ];

    public function user()
    {
        return $this->belongsTo(Portfolio::class, 'id_user');
    }
}
