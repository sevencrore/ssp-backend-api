<?php

namespace App\Exports;

use App\Models\ConfigSetting;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Events\AfterSheet;

/**
 * Internal Tracking Sheet - Sheet 1 of Payout Report
 *
 * Purpose: Internal record keeping and tracking of payouts
 *
 * Columns (18 total with headers):
 * SI.NO, User ID, Payout IDS, Total Amount, Admin Charge, Admin %,
 * TDS, TDS %, Payable Amount, Account Holder Name, Account Number,
 * Bank Name, Branch, IFSC Code, Phone Number, PAN Card, Aadhar Card, Debit Account
 *
 * Styling: Blue header, borders, alternating row colors, auto-sized columns
 *
 * @see PAYOUT_EXPORT_README.md for detailed documentation
 */
class InternalTrackingSheet implements FromCollection, WithHeadings, WithStyles, WithEvents, WithTitle, WithColumnFormatting
{
    protected $data;
    protected $debitAccountNumber;

    public function __construct($data)
    {
        $this->data = $data;

        // Fetch debit account number from config for reference
        $config = ConfigSetting::first();
        // $this->debitAccountNumber = $config->debit_account_number;
        $this->debitAccountNumber = '12456ADC453';
    }

    public function title(): string
    {
        return 'Internal Tracking';
    }

    /**
     * Format columns to display numbers without scientific notation
     * K = Account Number (beneficiary's account)
     * R = Debit Account (company's account)
     */
    public function columnFormats(): array
    {
        return [
            'K' => NumberFormat::FORMAT_NUMBER,  // Account Number
            'R' => NumberFormat::FORMAT_NUMBER,  // Debit Account
        ];
    }

    public function collection(): Collection
    {
        $counter = 1;
        $debitAccount = $this->debitAccountNumber;
        return collect($this->data)->map(function ($payout) use (&$counter, $debitAccount) {
            return [
                'SI.NO' => $counter++,
                'User ID' => $payout->user_id,
                'User Commission IDS' => is_array($payout->commission_history_ids) ? implode(', ', $payout->commission_history_ids) : $payout->commission_history_ids,
                'Total Amount' => $payout->total,
                'Admin Charge' => $payout->admin_charge,
                'Admin %' => ($payout->admin_percentage ?? 10) . '%',
                'TDS' => $payout->tds,
                'TDS %' => ($payout->tds_percentage ?? 2) . '%',
                'Payable Amount' => $payout->payable,
                'Account Holder Name' => $payout->bank_details->a_c_holder_name ?? '',
                'Account Number' => $payout->bank_details->account_number ?? '',
                'Bank Name' => $payout->bank_details->bank_name ?? '',
                'Branch' => $payout->bank_details->branch ?? '',
                'IFSC Code' => $payout->bank_details->ifsc_code ?? '',
                'Phone Number' => $payout->bank_details->phone_number ?? '',
                'PAN Card' => $payout->bank_details->pancard ?? '',
                'Aadhar Card' => $payout->bank_details->aadharcard ?? '',
                'Debit Account' => $debitAccount,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'SI.NO',
            'User ID',
            'User Commission IDS',
            'Total Amount',
            'Admin Charge',
            'Admin %',
            'TDS',
            'TDS %',
            'Payable Amount',
            'Account Holder Name',
            'Account Number',
            'Bank Name',
            'Branch',
            'IFSC Code',
            'Phone Number',
            'PAN Card',
            'Aadhar Card',
            'Debit Account',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        $sheet->getStyle('A1:R1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['argb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => '4F81BD']],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle("A1:R{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);

        for ($row = 2; $row <= $lastRow; $row++) {
            $color = $row % 2 === 0 ? 'F9F9F9' : 'FFFFFF';
            $sheet->getStyle("A{$row}:R{$row}")->applyFromArray([
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => $color]],
            ]);
        }

        $sheet->getStyle("A2:R{$lastRow}")->applyFromArray([
            'font' => ['size' => 10, 'color' => ['argb' => '000000']],
        ]);

        foreach (range('A', 'R') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle("A2:R{$lastRow}")->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);
            }
        ];
    }
}
