<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory;

    protected $table = 'portfolios'; // Явное указание таблицы

    protected $fillable = [
        'main_info', 
        'additional_info', 
        'media_links'
    ];

    // Обработка JSON полей
    protected $casts = [
        'main_info' => 'array',
        'additional_info' => 'array',
        'media_links' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }
}
