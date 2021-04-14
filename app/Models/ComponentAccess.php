<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComponentAccess extends Model
{
    use HasFactory;

    protected $table = 'component_access';
    protected $hidden = [
        'created_at',
        'updated_at',
        'updated_by',
        'deleted',
        'deleted_at'
    ];
}
