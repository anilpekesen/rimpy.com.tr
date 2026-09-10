<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    protected $fillable = ['from', 'to', 'status_code', 'hits', 'last_hit_at', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'last_hit_at' => 'datetime'];
    }
}
