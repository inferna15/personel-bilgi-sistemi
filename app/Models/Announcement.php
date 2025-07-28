<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'date',
        'content',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }
}
