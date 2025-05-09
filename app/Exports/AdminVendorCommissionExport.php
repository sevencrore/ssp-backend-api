<?php

namespace App\Exports;

use App\Models\VendorCommission;
use App\Models\Vendor;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class AdminVendorCommissionExport implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection(): Collection
    {
        $status = $this->request->has('status') ? (array) $this->request->status : [1, 2, 3];
        $startDate = $this->request->start_date;
        $endDate = $this->request->end_date;
        $vendorID = $this->request->vendor_id;

        $query = VendorCommission::query()->whereIn('status', $status);

        if ($vendorID) {
            $vendor = Vendor::find($vendorID);
            if (!$vendor) return collect([]);
            $query->where('vendor_id', $vendor->id);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        } elseif ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $statusLabels = [
            1 => 'Unpaid',
            2 => 'Paid',
            3 => 'Refunded',
            4 => 'Pending',
        ];

        return $query->orderBy('created_at', 'desc')->get()->map(function ($item) use ($statusLabels) {
            return [
                'Vendor ID'             => $item->vendor_id,
                'Order ID'              => $item->order_id,
                'Amount'                => $item->amount,
                'Admin Charges'         => $item->admin_charges_amount,
                'TDS Charges'           => $item->tds_charges_amount,
                'Expected Commission'   => $item->expected_commission,
                'Status'                => $statusLabels[$item->status] ?? 'Unknown',
                'Created At'            => $item->created_at->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Vendor ID',
            'Order ID',
            'Amount',
            'Admin Charges',
            'TDS Charges',
            'Expected Commission',
            'Status',
            'Created At',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        // Header styles
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['argb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['argb' => '4F81BD'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Borders for all data
        $sheet->getStyle('A1:H' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);

        // Alternate row colors
        for ($row = 2; $row <= $lastRow; $row++) {
            $fillColor = $row % 2 == 0 ? 'F9F9F9' : 'FFFFFF';
            $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['argb' => $fillColor],
                ],
            ]);
        }

        // Font size for data
        $sheet->getStyle('A2:H' . $lastRow)->applyFromArray([
            'font' => [
                'size' => 10,
                'color' => ['argb' => '000000'],
            ],
        ]);

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();

                $sheet->getStyle("A2:{$lastColumn}{$lastRow}")->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ]
                ]);
            },
        ];
    }
}
