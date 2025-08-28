<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'is_primary',
        'label',
        'address',
        'coordinate',
        'detail',
        'city_id'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'coordinate' => 'array',
        'detail' => 'object'
    ];

    public $timestamps = false;
}
