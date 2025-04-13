<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'company',
        'status',
        'source',
        'tags',
        'custom_fields',
    ];

    protected $casts = [
        'tags' => 'array',
        'custom_fields' => 'array',
    ];
}
