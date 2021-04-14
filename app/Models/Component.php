<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Component extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
        'updated_by',
        'deleted',
        'deleted_at'
    ];
}
