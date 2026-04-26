<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model {
    
    use HasFactory;

    protected $guarded = ['id'];

    public function seller() {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function location() {
        return $this->belongsTo(Location::class);
    }

    public function reviews() {
        return $this->hasMany(Review::class);
    }

    public function advertisements() {
        return $this->hasMany(Advertisement::class);
    }

    public function images() {
        return $this->hasMany(ServiceImage::class);
    }

    public function reportsReceived() {
        return $this->hasMany(Report::class, 'reported_service_id');
    }

    public function activeAdvertisement() {
        return $this->hasOne(Advertisement::class)
            ->where('is_active', true)
            ->where('end_date', '>', now());
    }
}
