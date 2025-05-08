<?php

namespace App\Exports;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class SkosListingExport implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection(): Collection
    {
        $request = $this->request;
        $supplierId = null;

        $user = User::find($request->user_id);

        if (!$user) return collect([]);

        if ($user->user_type == 99 || $user->user_type == 3) {
            if ($request->has('vendorUser_id')) {
                $vendorUser_id = $request->vendorUser_id;
                $supplier = Vendor::where('user_id', $vendorUser_id)->first();
                if ($supplier) {
                    $supplierId = $supplier->id;
                }
            }
        } else {
            $supplier = Vendor::where('user_id', $request->user_id)->first();
            if (!$supplier) return collect([]);
            $supplierId = $supplier->id;
        }

        $orderStatusFilter = $request->has('order_status')
            ? (array) $request->order_status
            : [0, 1];

        $categoryFilter = $request->category_id ?? '*';

        $query = OrderItem::query()
            ->select([
                'order_items.product_variant_id',
                DB::raw('SUM(order_items.quantity) AS total_quantity'),
                'order_items.product_id AS product_id',
                'order_items.unit_quantity',
                'order_items.unit_title',
            ])
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.order_status', $orderStatusFilter)
            ->groupBy(
                'order_items.product_variant_id',
                'order_items.product_id',
                'order_items.unit_quantity',
                'order_items.unit_title'
            );

        if ($supplierId) {
            $query->where('orders.vendor_id', $supplierId);
        }

        if ($categoryFilter !== '*') {
            $query->whereHas('product', function ($productQuery) use ($categoryFilter) {
                $productQuery->where('category_id', $categoryFilter);
            });
        }

        $items = $query->get();

        // Add product and variant details
        return $items->map(function ($item) {
            $product = Product::with('category')->find($item->product_id);
            $variant = ProductVariant::find($item->product_variant_id);
            
            return [
                'Product Title' => $product->title ?? 'N/A',
                'Variant Title' => $variant->title ?? 'N/A',
                'Unit Quantity' => $item->unit_quantity,
                'Unit Title' => $item->unit_title,
                'Total Quantity' => $item->total_quantity,
                'Category' => $product->category->title ?? 'N/A',
                'Image URL' => $product->image_url ?? 'N/A',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Product Title',
            'Variant Title',
            'Unit Quantity',
            'Unit Title',
            'Total Quantity',
            'Category',
            'Image URL',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Define the last row dynamically based on the number of items in the sheet
        $lastRow = $sheet->getHighestRow();
    
        // Apply bold and center alignment to the headers
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['argb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['argb' => '4F81BD'],  // Light Blue color for header background
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);
    
        // Add border around all cells (headers and data rows)
        $sheet->getStyle('A1:G' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],  // Black border
                ],
            ],
        ]);
    
        // Apply alternating row colors for better readability
        for ($row = 2; $row <= $lastRow; $row++) {
            $color = $row % 2 == 0 ? 'F9F9F9' : 'FFFFFF';  // Light Grey for even rows, White for odd rows
            $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['argb' => $color],  // Alternating row colors
                ],
            ]);
        }
    
        // Apply font size and color for data rows
        $sheet->getStyle('A2:G' . $lastRow)->applyFromArray([
            'font' => [
                'size' => 10,
                'color' => ['argb' => '000000'],  // Black color for font
            ],
        ]);

        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    public function registerEvents(): array
{
    return [
        AfterSheet::class => function (AfterSheet $event) {
            $sheet = $event->sheet->getDelegate();
            
            // Get last row dynamically
            $lastRow = $sheet->getHighestRow();
            $lastColumn = $sheet->getHighestColumn();

            // Apply center alignment to all cells from A2 to last cell
            $cellRange = 'A2:' . $lastColumn . $lastRow;
            $sheet->getStyle($cellRange)->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ]
            ]);
        }
    ];
}
}

