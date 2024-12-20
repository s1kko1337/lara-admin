<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    protected $table = 'works';

    protected $fillable = [
        'modeler_id',
        'path_to_model',
        'additional_info',
        'tags',
        'created_at',
        'updated_at',
        'model_name',
        'binary_file',
        'binary_preview',
    ];

    protected $casts = [
        'binary_file' => 'string',
        'binary_preview' => 'string',
    ];

}
