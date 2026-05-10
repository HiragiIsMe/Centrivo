<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerProfile extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }



    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    public function isPending(): bool
    {
        return $this->verification_status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->verification_status === 'rejected';
    }

    public function isUnverified(): bool
    {
        return $this->verification_status === 'unverified';
    }

    public function canCreateService(): bool
    {
        return $this->verification_status === 'verified';
    }

    public function getVerificationStatusLabelAttribute(): string
    {
        return match($this->verification_status) {
            'unverified' => 'Belum Verifikasi',
            'pending'    => 'Menunggu Review',
            'verified'   => 'Terverifikasi',
            'rejected'   => 'Ditolak',
            default      => 'Tidak Diketahui',
        };
    }

    public function getVerificationStatusColorAttribute(): string
    {
        return match($this->verification_status) {
            'unverified' => 'text-slate-500 bg-slate-100',
            'pending'    => 'text-yellow-600 bg-yellow-100',
            'verified'   => 'text-green-600 bg-green-100',
            'rejected'   => 'text-red-600 bg-red-100',
            default      => 'text-gray-500 bg-gray-100',
        };
    }
}
