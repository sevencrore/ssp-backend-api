<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Payout Report Export - Multi-sheet Excel Export
 *
 * Generates single Excel file with 2 sheets:
 * - Sheet 1: Internal Tracking (with headers & styling)
 * - Sheet 2: Bank Format (no headers, strict bank format for NEFT upload)
 *
 * @see PAYOUT_EXPORT_README.md for detailed documentation
 */
class PayoutReportExport implements WithMultipleSheets
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Returns array of sheets
     * Sheet 1: Internal Tracking (with headers and styling)
     * Sheet 2: Bank Format (no headers, strict bank format)
     */
    public function sheets(): array
    {
        return [
            new InternalTrackingSheet($this->data),
            new BankFormatSheet($this->data),
        ];
    }
}