<?php

namespace App\Http\Controllers;

use App\Models\CustomerVendor;
use App\Http\Controllers\API\UsersController;
use App\Models\ConfigSetting;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerVendorController extends Controller
{
    // Store a new customer-vendor relationship
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer|exists:users,id', // Assuming a Customer model exists
            'vendor_id' => 'required|integer|exists:vendors,id', // Assuming a Vendor model exists
        ]);

        $customerVendor = CustomerVendor::create([
            'customer_id' => $request->customer_id,
            'vendor_id' => $request->vendor_id,
        ]);
        Log::info(" in the customer vendor $customerVendor");
        if ($customerVendor) {
            $userController = new UsersController();
            $update = $userController->changeUserState($customerVendor->customer_id, 0);
        } else {
            return response()->json(['message' => 'failed to Create Customer-Vendor relationship'], 500);
        }

        return response()->json(['message' => 'Customer-Vendor relationship created successfully', 'data' => $customerVendor], 201);
    }

    //update customer vendor relation ship 
    public function updateVendor(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer|exists:users,id',
            'vendor_id' => 'required|integer|exists:vendors,id',
        ]);

        try {
            // Find the first CustomerVendor record for this customer
            $customerVendor = CustomerVendor::where('customer_id', $request->customer_id)->first();

            if (!$customerVendor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer-Vendor relationship not found for this customer',
                ], 404);
            }

            // Update the vendor_id
            $customerVendor->vendor_id = $request->vendor_id;
            $customerVendor->save();

            $userController = new UsersController();
            $userController->changeUserState($request->customer_id, 0);

            return response()->json([
                'success' => true,
                'message' => 'Customer-Vendor relationship updated successfully',
                'data' => $customerVendor,
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error updating CustomerVendor: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the Customer-Vendor relationship',
            ], 500);
        }
    }


    //  auto assign the vendor to customer 
    public function autoAsssignCustomerVendor($customerId, $pincode)
    {
        $vendor = Vendor::where('pincode', $pincode)->first();

        if (!$vendor) {
            $config_settings = ConfigSetting::find(1);
            // $vendor = Vendor::find(1);
            $vendor_id = $config_settings->default_vendor_id;
        } else {
            $vendor_id = $vendor->id;
        }
        $customerVendor = CustomerVendor::create([
            'customer_id' => $customerId,
            // 'vendor_id' => $vendor->id,
            'vendor_id' => $vendor_id,
        ]);

        Log::info("Created Customer-Vendor relationship: $customerVendor");

        if ($customerVendor) {
            $userController = new UsersController();
            $userController->changeUserState($customerVendor->customer_id, 0);
        } else {
            throw new \Exception('Failed to create Customer-Vendor relationship');
        }

        return $customerVendor;
    }


    // Get all customer-vendor relationships
    public function index()
    {
        $customerVendors = CustomerVendor::all();
        return response()->json(['data' => $customerVendors], 200);
    }

    // Delete a customer-vendor relationship
    public function destroy($id)
    {
        $customerVendor = CustomerVendor::find($id);

        if (!$customerVendor) {
            return response()->json(['message' => 'Customer-Vendor relationship not found'], 404);
        }

        $customerVendor->delete();

        return response()->json(['message' => 'Customer-Vendor relationship deleted successfully'], 200);
    }
}
