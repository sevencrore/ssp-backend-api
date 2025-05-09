<?php

namespace App\Exports;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class AdminVendorCommissionSummaryExport implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection(): Collection
    {
        $search = $this->request->query('search');
        $now = Carbon::now()->toDateString();

        $query = Vendor::query();

        $query->withCount([
            'vendorCommissions as commission_count' => function ($q) use ($now) {
                $q->where('status', 1)
                  ->whereDate('created_at', '<=', $now);
            }
        ]);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%$search%")
                    ->orWhere('middle_name', 'LIKE', "%$search%")
                    ->orWhere('last_name', 'LIKE', "%$search%")
                    ->orWhere('business_name', 'LIKE', "%$search%");
            });
        }

        $vendors = $query->orderByDesc('commission_count')->get();

        return $vendors->map(function ($vendor) {
            return [
                'Vendor ID'         => $vendor->id,
                'User ID'           => $vendor->user_id,
                'Business Name'     => $vendor->business_name,
                'First Name'        => $vendor->first_name,
                'Last Name'         => $vendor->last_name,
                'Phone 1'           => $vendor->phone_1,
                'Phone 2'           => $vendor->phone_2,
                'Aadhar Number'     => $vendor->aadhar_number,
                'Address'           => $vendor->address,
                'Pincode'           => $vendor->pincode,
               'Commission Count'   => (int) $vendor->commission_count,
                'Created At'        => $vendor->created_at->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Vendor ID',
            'User ID',
            'Business Name',
            'First Name',
            'Last Name',
            'Phone 1',
            'Phone 2',
            'Aadhar Number',
            'Address',
            'Pincode',
            'Commission Count',
            'Created At',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        $sheet->getStyle('A1:L1')->applyFromArray([
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

        $sheet->getStyle("A1:L{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);

        for ($row = 2; $row <= $lastRow; $row++) {
            $fillColor = $row % 2 == 0 ? 'F9F9F9' : 'FFFFFF';
            $sheet->getStyle("A{$row}:L{$row}")->applyFromArray([
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['argb' => $fillColor],
                ],
            ]);
        }

        $sheet->getStyle("A2:L{$lastRow}")->applyFromArray([
            'font' => [
                'size' => 10,
                'color' => ['argb' => '000000'],
            ],
        ]);

        foreach (range('A', 'L') as $col) {
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
