<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageSection extends Model
{
    protected $guarded = [];

    protected $casts = ['extra' => 'array', 'is_active' => 'boolean'];
}
