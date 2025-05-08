<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\SkosListingExport;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExportController extends Controller
{
    public function exportSkosListing(Request $request)
    {
        return Excel::download(new SkosListingExport($request), 'skos_listing_export.xlsx');
    }
}
