<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvertisementTransaction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function advertisement() {
        return $this->belongsTo(Advertisement::class);
    }

    public function seller() {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function adPackage() {
        return $this->belongsTo(AdPackage::class);
    }
}
