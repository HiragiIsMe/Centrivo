<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Advertisement;
use App\Models\AdvertisementTransaction;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Notification;

class WebhookController extends Controller
{
    public function midtransCallback(Request $request)
    {
        // Set konfigurasi midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        try {
            $notif = new Notification();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to process notification'], 500);
        }

        $transactionStatus = $notif->transaction_status;
        $paymentType = $notif->payment_type;
        $orderId = $notif->order_id;
        $fraudStatus = $notif->fraud_status;

        // Determine transaction type from order_id prefix
        if (str_starts_with($orderId, 'ADV-')) {
            return $this->handleAdPayment($orderId, $transactionStatus, $paymentType, $fraudStatus);
        }

        return $this->handleServicePayment($orderId, $transactionStatus, $paymentType, $fraudStatus);
    }

    private function handleServicePayment($orderId, $transactionStatus, $paymentType, $fraudStatus)
    {
        // Extract ID: format SRV-{id}-{timestamp} or legacy {id}-{timestamp}
        $cleanId = $orderId;
        if (str_starts_with($orderId, 'SRV-')) {
            $cleanId = substr($orderId, 4); // remove "SRV-"
        }
        $idParts = explode('-', $cleanId);
        $transactionId = $idParts[0];

        $transaction = Transaction::find($transactionId);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        if ($transactionStatus == 'capture') {
            if ($paymentType == 'credit_card') {
                if ($fraudStatus == 'challenge') {
                    $transaction->update(['payment_status' => 'pending']);
                } else {
                    $transaction->update([
                        'payment_status' => 'paid',
                        'transaction_status' => 'accepted'
                    ]);
                }
            }
        } else if ($transactionStatus == 'settlement') {
            $transaction->update([
                'payment_status' => 'paid',
                'transaction_status' => 'accepted'
            ]);
        } else if ($transactionStatus == 'pending') {
            $transaction->update(['payment_status' => 'pending']);
        } else if ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
            $transaction->update([
                'payment_status' => 'failed',
                'transaction_status' => 'cancelled'
            ]);
        }

        return response()->json(['message' => 'Service notification processed']);
    }

    private function handleAdPayment($orderId, $transactionStatus, $paymentType, $fraudStatus)
    {
        // Extract ID: format ADV-{id}-{timestamp}
        $cleanId = substr($orderId, 4); // remove "ADV-"
        $idParts = explode('-', $cleanId);
        $adTxId = $idParts[0];

        $adTx = AdvertisementTransaction::with('advertisement.service')->find($adTxId);

        if (!$adTx) {
            return response()->json(['message' => 'Ad transaction not found'], 404);
        }

        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            $adTx->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);

            // Activate the advertisement
            $ad = $adTx->advertisement;
            $ad->update([
                'is_active' => true,
                'start_date' => now(),
                'end_date' => now()->addDays($adTx->duration_days),
            ]);
        } else if ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
            $adTx->update(['payment_status' => 'failed']);
        }

        return response()->json(['message' => 'Ad notification processed']);
    }
}
