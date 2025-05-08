<?php

namespace App\Exports;

use App\Models\UserPayment;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class AdminProductTransactionsExport implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection(): Collection
    {
        $query = UserPayment::orderBy('created_at', 'desc');

        if ($this->request->has('status')) {
            $query->where('status', $this->request->status);
        }

        if (!empty($this->request->start_date) && !empty($this->request->end_date)) {
            $query->whereBetween('created_at', [$this->request->start_date, $this->request->end_date]);
        } elseif (!empty($this->request->start_date)) {
            $query->where('created_at', '>=', $this->request->start_date);
        } elseif (!empty($this->request->end_date)) {
            $query->where('created_at', '<=', $this->request->end_date);
        }

        return $query->get()->map(function ($item) {
            return [
                'User ID'              => $item->user_id,
                'Order ID'             => $item->order_id,
                'Transaction ID'       => $item->transaction_id,
                'Amount'               => $item->amount,
                'Email'                => $item->email,
                'Razorpay Order ID'    => $item->razorpay_order_id,
                'Razorpay Payment ID'  => $item->razorpay_payment_id,
                'Razorpay Signature'   => $item->razorpay_signature,
                'Status'               => $item->status,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'User ID',
            'Order ID',
            'Transaction ID',
            'Amount',
            'Email',
            'Razorpay Order ID',
            'Razorpay Payment ID',
            'Razorpay Signature',
            'Status',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['argb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => '4F81BD']],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle('A1:I' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);

        for ($row = 2; $row <= $lastRow; $row++) {
            $color = $row % 2 == 0 ? 'F9F9F9' : 'FFFFFF';
            $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['argb' => $color],
                ],
            ]);
        }

        $sheet->getStyle('A2:I' . $lastRow)->applyFromArray([
            'font' => ['size' => 10, 'color' => ['argb' => '000000']],
        ]);

        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $cellRange = 'A2:I' . $lastRow;

                $sheet->getStyle($cellRange)->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);
            }
        ];
    }
}
