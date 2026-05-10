<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'is_disputed' => 'boolean',
        'disputed_at' => 'datetime',
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id');
    }

    public function disputeReport()
    {
        return $this->hasOne(Report::class, 'related_transaction_id');
    }

    /**
     * Scope: transaksi yang sedang disputed
     */
    public function scopeDisputed($query)
    {
        return $query->where('is_disputed', true);
    }

    /**
     * Scope: transaksi aktif (sudah dibayar, belum selesai, tidak disputed)
     */
    public function scopeActiveNormal($query)
    {
        return $query->where('payment_status', 'paid')
                     ->where('transaction_status', '!=', 'completed')
                     ->where('is_disputed', false);
    }

    /**
     * Apakah transaksi ini sedang bermasalah?
     */
    public function getIsDisputedAttribute($value): bool
    {
        return (bool) $value;
    }
}
