<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billboard extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'gradient_from',
        'gradient_to',
        'image_path',
        'order',
        'is_active'
    ];
}
