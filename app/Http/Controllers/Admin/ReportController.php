<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use App\Models\AdvertisementTransaction;
use App\Models\Withdrawal;
use App\Models\Setting;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function exportExcel()
    {
        $spreadsheet = new Spreadsheet();
        
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Platform Overview');
        
        $totalUsers = User::where('role', 'user')->count();
        $totalSellers = User::where('role', 'seller')->count();
        $completedTransactions = Transaction::where('transaction_status', 'completed')->count();
        $serviceFeeRevenue = Transaction::where('transaction_status', 'completed')->sum('admin_fee');
        $adRevenue = AdvertisementTransaction::where('payment_status', 'paid')->sum('amount');
        
        $sheet->setCellValue('A1', 'PLATFORM REPORT - CENTRIVO');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->setCellValue('A2', 'Generated at: ' . now()->format('d M Y H:i'));

        $sheet->setCellValue('A4', 'METRIK UTAMA');
        $sheet->getStyle('A4')->getFont()->setBold(true);
        
        $metrics = [
            ['Total User', $totalUsers],
            ['Total Seller', $totalSellers],
            ['Total Transaksi Selesai', $completedTransactions],
            ['Total Revenue Platform', $serviceFeeRevenue + $adRevenue],
            ['Revenue Fee Layanan', $serviceFeeRevenue],
            ['Revenue Iklan', $adRevenue],
        ];

        $row = 5;
        foreach ($metrics as $metric) {
            $sheet->setCellValue('A' . $row, $metric[0]);
            $sheet->setCellValue('B' . $row, $metric[1]);
            if (str_contains($metric[0], 'Revenue')) {
                $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0');
            }
            $row++;
        }

        $txSheet = $spreadsheet->createSheet();
        $txSheet->setTitle('Service Transactions');
        $transactions = Transaction::with(['serviceRequest.service', 'serviceRequest.buyer', 'serviceRequest.service.seller'])
            ->where('transaction_status', 'completed')
            ->get();
            
        $txSheet->setCellValue('A1', 'DAFTAR TRANSAKSI LAYANAN SELESAI');
        $headers = ['No', 'ID TX', 'Layanan', 'Pelanggan', 'Seller', 'Harga Dasar (Rp)', 'PPN (Rp)', 'Fee Admin (Rp)', 'Total (Rp)', 'Tanggal Selesai'];
        $column = 'A';
        foreach ($headers as $h) {
            $txSheet->setCellValue($column . '3', $h);
            $txSheet->getStyle($column . '3')->getFont()->setBold(true);
            $column++;
        }
        
        $row = 4;
        $no = 1;
        foreach ($transactions as $tx) {
            $txSheet->setCellValue('A' . $row, $no++);
            $txSheet->setCellValue('B' . $row, 'TX-' . $tx->id);
            $txSheet->setCellValue('C' . $row, $tx->serviceRequest->service->service_name);
            $txSheet->setCellValue('D' . $row, $tx->serviceRequest->buyer->email);
            $txSheet->setCellValue('E' . $row, $tx->serviceRequest->service->seller->email);
            $txSheet->setCellValue('F' . $row, $tx->base_price);
            $txSheet->setCellValue('G' . $row, $tx->tax_amount);
            $txSheet->setCellValue('H' . $row, $tx->admin_fee);
            $txSheet->setCellValue('I' . $row, $tx->final_price);
            $txSheet->setCellValue('J' . $row, $tx->completed_at ? Carbon::parse($tx->completed_at)->format('d/m/Y') : '-');
            $txSheet->getStyle('F' . $row . ':I' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $row++;
        }

        $adSheet = $spreadsheet->createSheet();
        $adSheet->setTitle('Ad Transactions');
        $ads = AdvertisementTransaction::with(['advertisement.service', 'seller', 'adPackage'])
            ->where('payment_status', 'paid')
            ->get();
            
        $adSheet->setCellValue('A1', 'DAFTAR TRANSAKSI IKLAN');
        $headers = ['No', 'ID AD', 'Seller', 'Layanan', 'Paket', 'Harga (Rp)', 'Tanggal Bayar'];
        $column = 'A';
        foreach ($headers as $h) {
            $adSheet->setCellValue($column . '3', $h);
            $adSheet->getStyle($column . '3')->getFont()->setBold(true);
            $column++;
        }
        
        $row = 4;
        $no = 1;
        foreach ($ads as $ad) {
            $adSheet->setCellValue('A' . $row, $no++);
            $adSheet->setCellValue('B' . $row, 'ADV-' . $ad->id);
            $adSheet->setCellValue('C' . $row, $ad->seller->email);
            $adSheet->setCellValue('D' . $row, $ad->advertisement->service->service_name);
            $adSheet->setCellValue('E' . $row, $ad->adPackage->name ?? $ad->duration_days . ' Days');
            $adSheet->setCellValue('F' . $row, $ad->amount);
            $adSheet->setCellValue('G' . $row, $ad->paid_at ? Carbon::parse($ad->paid_at)->format('d/m/Y') : '-');
            $adSheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $row++;
        }

        foreach ($spreadsheet->getAllSheets() as $sh) {
            foreach (range('A', 'H') as $col) {
                $sh->getColumnDimension($col)->setAutoSize(true);
            }
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Platform_Report_' . Carbon::now()->format('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
