<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'request_id');
    }

    public function latestTransaction()
    {
        return $this->hasOne(Transaction::class, 'request_id')->latestOfMany();
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'request_id');
    }
}
