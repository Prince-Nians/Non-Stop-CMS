<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageSeo extends Model
{
    protected $guarded = [];

    protected $casts = ['no_index' => 'boolean', 'custom_meta' => 'array'];
}
