<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Generate kode unik laporan format: RPT-YYYYMMDD-XXXX
     */
    public static function generateCode(): string
    {
        $date = now()->format('Ymd');
        // Ambil nomor urut hari ini
        $todayCount = static::whereDate('created_at', today())->count() + 1;
        return 'RPT-' . $date . '-' . str_pad($todayCount, 4, '0', STR_PAD_LEFT);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function reportedUser()
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    public function reportedService()
    {
        return $this->belongsTo(Service::class, 'reported_service_id');
    }

    public function relatedTransaction()
    {
        return $this->belongsTo(Transaction::class, 'related_transaction_id');
    }

    /**
     * Scope untuk laporan yang pending (belum ditinjau)
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Label tipe laporan
     */
    public function getTypeLabel(): string
    {
        if ($this->reported_service_id && $this->reported_user_id) {
            return 'Service + User';
        }
        if ($this->reported_service_id) {
            return 'Service';
        }
        return 'User';
    }

    /**
     * Apakah laporan ini adalah laporan akibat ban admin?
     */
    public function isBanReport(): bool
    {
        return !empty($this->ban_reason);
    }
}
