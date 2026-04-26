<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function service() {
        return $this->belongsTo(Service::class);
    }

    public function transactions() {
        return $this->hasMany(AdvertisementTransaction::class);
    }

    public function isCurrentlyActive() {
        return $this->is_active && $this->end_date && $this->end_date->isFuture();
    }
}
