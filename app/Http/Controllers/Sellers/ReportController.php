<?php

namespace App\Http\Controllers\Sellers;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $seller = Auth::user();

        // Get completed transactions for the table
        $transactions = Transaction::whereHas('serviceRequest.service', function ($query) use ($seller) {
            $query->where('seller_id', $seller->id);
        })
        ->where('transaction_status', 'completed')
        ->with(['serviceRequest.service', 'serviceRequest.buyer.userProfile'])
        ->latest('completed_at')
        ->paginate(15);

        // Stats for report page
        $totalEarned = Transaction::whereHas('serviceRequest.service', function ($query) use ($seller) {
            $query->where('seller_id', $seller->id);
        })->where('transaction_status', 'completed')->sum('base_price');

        $thisMonthEarned = Transaction::whereHas('serviceRequest.service', function ($query) use ($seller) {
            $query->where('seller_id', $seller->id);
        })
        ->where('transaction_status', 'completed')
        ->whereMonth('completed_at', now()->month)
        ->whereYear('completed_at', now()->year)
        ->sum('base_price');

        return view('sellers-dashboard.reports', compact('transactions', 'totalEarned', 'thisMonthEarned'));
    }

    public function exportExcel()
    {
        $seller = Auth::user();
        $transactions = Transaction::whereHas('serviceRequest.service', function ($query) use ($seller) {
            $query->where('seller_id', $seller->id);
        })
        ->where('transaction_status', 'completed')
        ->with(['serviceRequest.service', 'serviceRequest.buyer.userProfile'])
        ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'LAPORAN PENDAPATAN SELLER - CENTRIVO');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

        $sheet->setCellValue('A2', 'Seller: ' . ($seller->sellerProfile->brand_name ?? $seller->email));
        $sheet->setCellValue('A3', 'Tanggal Export: ' . now()->format('d M Y H:i'));

        // Table Headers
        $headers = ['No', 'ID Transaksi', 'Layanan', 'Pelanggan', 'Tanggal Selesai', 'Pendapatan (Rp)'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '5', $header);
            $sheet->getStyle($column . '5')->getFont()->setBold(true);
            $sheet->getStyle($column . '5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');
            $column++;
        }

        // Data
        $row = 6;
        $no = 1;
        $total = 0;
        foreach ($transactions as $tx) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, 'TX-' . $tx->id);
            $sheet->setCellValue('C' . $row, $tx->serviceRequest->service->service_name);
            $sheet->setCellValue('D' . $row, $tx->serviceRequest->buyer->userProfile->name ?? $tx->serviceRequest->user->email);
            $sheet->setCellValue('E' . $row, $tx->completed_at ? Carbon::parse($tx->completed_at)->format('d/m/Y') : '-');
            $sheet->setCellValue('F' . $row, $tx->base_price);
            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0');
            
            $total += $tx->base_price;
            $row++;
        }

        // Total
        $sheet->setCellValue('E' . $row, 'TOTAL PENDAPATAN');
        $sheet->setCellValue('F' . $row, $total);
        $sheet->getStyle('E' . $row . ':F' . $row)->getFont()->setBold(true);
        $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0');

        // Column Auto Width
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Income_Report_' . Carbon::now()->format('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
