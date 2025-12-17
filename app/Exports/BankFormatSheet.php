<?php

namespace App\Exports;

use App\Models\User;
use App\Models\ConfigSetting;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

/**
 * Bank Format Sheet - Sheet 2 of Payout Report
 *
 * Purpose: Upload to bank for bulk NEFT transfers
 * Based on: Generic_Data_Upload_File_format_WO_only_epayments.xls
 *
 * IMPORTANT: NO HEADERS - Data starts from row 1 (strict bank requirement)
 *
 * Column Format (12 columns):
 * ---------------------------
 * 1. Payment Method Identifier - Text(1) - N=NEFT, R=RTGS, I=Internal Fund Transfer
 * 2. Amount - Numeric(12.2) - Payable amount after deductions
 * 3. Value Date - Date(dd-mm-yyyy) - Transaction date (today)
 * 4. Beneficiary Name - String(50) - Account holder name
 * 5. Bene Account Number - Text(20) - Beneficiary account number
 * 6. Email ID of beneficiary - Text(1000) - User email (nullable)
 * 7. Email Body - Text(1000) - Always empty
 * 8. Debit Account Number - Numeric(15) - Company's bank account (from config_setting table)
 * 9. CRN (Narration/Remarks) - Alphanumeric(15) - Format: PO-{user_id}
 * 10. Receiver IFSC - Alphanumeric(11) - Bank IFSC code
 * 11. Receiver A/c type - Numeric(2) - 10=Savings, 11=Current
 * 12. Remarks - Text(15) - Beneficiary name (truncated to 15 chars)
 *
 * @see PAYOUT_EXPORT_README.md for detailed documentation
 */
class BankFormatSheet implements FromCollection, WithTitle, WithColumnFormatting
{
    protected $data;

    // Fixed values as per bank requirement
    const PAYMENT_METHOD = 'N'; // NEFT
    const ACCOUNT_TYPE = '10'; // Savings

    protected $debitAccountNumber;

    public function __construct($data)
    {
        $this->data = $data;

        // Fetch debit account number from config (has default in DB)
        $config = ConfigSetting::first();
        $this->debitAccountNumber = '12456ADC453';
    }

    public function title(): string
    {
        return 'Bank Format';
    }

    /**
     * Format columns to display numbers without scientific notation
     * E = Bene Account Number (customer's account)
     * H = Debit Account Number (company's account)
     */
    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER,  // Bene Account Number
            'H' => NumberFormat::FORMAT_NUMBER,  // Debit Account Number
        ];
    }

    public function collection(): Collection
    {
        return collect($this->data)->map(function ($payout) {
            // Fetch user email (nullsafe in case user doesn't exist)
            $user = User::find($payout->user_id);
            $email = $user?->email ?? '';

            // Get beneficiary name (max 50 chars)
            $beneficiaryName = $payout->bank_details->a_c_holder_name ?? '';
            $beneficiaryName = substr($beneficiaryName, 0, 50);

            // CRN: PO-{user_id} (max 15 chars)
            $crn = 'PO-' . $payout->user_id;
            $crn = substr($crn, 0, 15);

            // Remarks: beneficiary name (max 15 chars)
            $remarks = substr($beneficiaryName, 0, 15);

            return [
                self::PAYMENT_METHOD,                                   // 1. Payment Method Identifier
                number_format($payout->payable, 2, '.', ''),            // 2. Amount (12.2 format)
                date('d-m-Y'),                                          // 3. Value Date (dd-mm-yyyy)
                $beneficiaryName,                                       // 4. Beneficiary Name
                $payout->bank_details->account_number ?? '',            // 5. Bene Account Number
                $email,                                                 // 6. Email ID of beneficiary
                '',                                                     // 7. Email Body (always empty)
                $this->debitAccountNumber,                                  // 8. Debit Account Number
                $crn,                                                   // 9. CRN (Narration/Remarks)
                $payout->bank_details->ifsc_code ?? '',                 // 10. Receiver IFSC
                self::ACCOUNT_TYPE,                                     // 11. Receiver A/c type (Savings)
                $remarks,                                               // 12. Remarks
            ];
        });
    }
}
