<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    use Notifiable;

    protected $guarded = ['id'];

    public function userProfile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function location()
    {
        return $this->hasOne(Location::class);
    }

    public function sellerProfile()
    {
        return $this->hasOne(SellerProfile::class);
    }

    public function services() {
        return $this->hasMany(Service::class, 'seller_id');
    }

    public function reviews() {
        return $this->hasMany(Review::class);
    }

    public function reportsReceived() {
        return $this->hasMany(Report::class, 'reported_user_id');
    }
}