<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function reporter() {
        return $this->belongsTo(User::class, 'reporter_id');
    }
    
    public function reportedUser() {
        return $this->belongsTo(User::class, 'reported_user_id');
    }
    
    public function reportedService() {
        return $this->belongsTo(Service::class, 'reported_service_id');
    }
}
