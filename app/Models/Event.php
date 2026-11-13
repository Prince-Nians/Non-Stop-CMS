<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $guarded = [];

    protected $casts = ['is_published' => 'boolean', 'starts_at' => 'datetime', 'published_at' => 'datetime'];
}
