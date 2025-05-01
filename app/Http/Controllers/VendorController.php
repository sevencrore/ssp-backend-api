<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\VendorCommissionController;
use App\Models\Vendor;
use App\Models\VendorCommission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
// use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::all();
        return response()->json($vendors);
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'business_name' => 'required|string',
            'phone_1' => 'required|digits:10',
            'aadhar_number' => 'required|string|size:12',
            'address' => 'required|string',
            'pincode' => 'required|digits:10',
            'longitude' => 'required|numeric',
            'latitude' => 'nullable|numeric',
        ]);

        $vendor = Vendor::create($request->all());
        return response()->json($vendor, 201);
    }

    public function show($id)
    {
        $vendor = Vendor::findOrFail($id);
        return response()->json($vendor);
    }

    public function update(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string',
            'business_name' => 'required|string',
            'phone_1' => 'required|digits:10',
            'aadhar_number' => 'required|string|size:12',
            'address' => 'required|string',
            'pincode' => 'required|digits:10',
            'longitude' => 'required|numeric',
            'latitude' => 'nullable|numeric',
        ]);

        $vendor->update($request->all());
        return response()->json($vendor);
    }

    public function destroy($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->delete();
        return response()->json(null, 204);
    }
    public function getAllPaginated(Request $request): JsonResponse
    {
        try {
            $search = $request->query('search');
            $perPage = $request->query('per_page', 10); // default 10
    
            $now = Carbon::now()->toDateString();
    
            $query = Vendor::query();
    
            // Apply conditional commission count using withCount
            $query->withCount([
                'vendorCommissions as commission_count' => function ($q) use ($now) {
                    $q->where('status', 1)
                      ->whereDate('created_at', '<=', $now);
                }
            ]);
    
            // Apply search filter
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%$search%")
                        ->orWhere('middle_name', 'LIKE', "%$search%")
                        ->orWhere('last_name', 'LIKE', "%$search%")
                        ->orWhere('business_name', 'LIKE', "%$search%");
                });
            }
    
            // Order by the computed commission count
            $query->orderByDesc('commission_count');
    
            // Paginate
            $vendors = $query->paginate($perPage)->appends([
                'search' => $search,
                'per_page' => $perPage
            ]);

            // get today stats of comission paid
            $vendorcomissioncontroller = new VendorCommissionController();
            $stats = $vendorcomissioncontroller->getTodayCommissionStats();
            if(!$stats['success']){
                throw new \Exception($stats['message']);
            }
    
            return response()->json([
                'success' => true,
                'vendors' => $vendors->items(),
                'total_commission_count' =>$stats['total_commission_count'],
                'pending_commission_count' =>$stats['pending_commission_count'],
                'pending_percentage' =>$stats['pending_percentage'],
                'total' => $vendors->total(),
                'current_page' => $vendors->currentPage(),
                'per_page' => $vendors->perPage(),
                'last_page' => $vendors->lastPage(),
                'has_next_page' => $vendors->hasMorePages(),
                'has_previous_page' => $vendors->currentPage() > 1,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve vendors',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function total_vendors_count()
    {
        try {
            $count = Vendor::count();
            Log::info('Fetched total vendor count successfully.', ['count' => $count]);

            return[
                'success' => true,
                'count' => $count,
            ];
        } catch (\Exception $e) {
            return[
                'success' => false,
                'message' => 'Failed to fetch total vendors.',
                'error' => $e->getMessage(),
            ];
        }
    }

}
