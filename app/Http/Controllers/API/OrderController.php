<?php

namespace App\Http\Controllers\API;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Cart;
use App\Models\UserDetails;
use App\Models\Comission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\API\EarningController;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\CustomerVendor;
use App\Models\ConfigSetting;
use App\Models\Vendor;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Schema(
 *     schema="Order",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(property="address_id", type="integer"),
 *     @OA\Property(property="order_status", type="integer"),
 *     @OA\Property(property="tracking_number", type="string"),
 *     @OA\Property(property="total_amount", type="integer"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

class OrderController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/orders",
     *     tags={"Orders"},
     *     summary="Retrieve all Orders",
     *     @OA\Response(
     *         response=200,
     *         description="Retrieve all Orders successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Order")),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $orders = Order::all();
        return response()->json([
            'success' => true,
            'data' => $orders,
            'message' => 'Retrieve all Orders successfully',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     tags={"Orders"},
     *     summary="Create a new Order",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"order_status", "total_amount"},
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="address_id", type="integer"),
     *             @OA\Property(property="order_status", type="integer"),
     *             @OA\Property(property="tracking_number", type="string"),
     *             @OA\Property(property="total_amount", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", ref="#/components/schemas/Order"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|integer',
            'address_id' => 'nullable|integer',
            'order_status' => 'required|integer',
            'tracking_number' => 'nullable|string',
            'total_amount' => 'required|integer',
        ]);

        $order = Order::create($request->all());
        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Order created successfully',
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/orders/{order}",
     *     tags={"Orders"},
     *     summary="Retrieve a specific Order",
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Specified Order displayed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", ref="#/components/schemas/Order"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */


    /**
     * Store order and order items.
     */
    public function storeOrder(Request $request)
    {
        // Validate request payload
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'subtotal' => 'required|numeric',
            'savings' => 'required|numeric',
            'grand_total' => 'required|numeric',
            'cart_data' => 'required|array',
        ]);
        $validated['user_id'] = $request->user_id;
        $userDetail = UserDetails::where('user_id', $validated['user_id'])->first();
        $comission_id = $userDetail->comission_id;

        $comission = Comission::where('id', $comission_id)->first();

        if ($validated['grand_total'] < $comission->minimum_order) {
            return response()->json([
                'message' => "The minimum amount to place the order is $comission->minimum_order",
            ], 500);
        }

        // Generate the current year and month (yyyymm)
        $currentYearMonth = now()->format('Ym');

        // Find the last order's tracking number for the current month
        $lastOrder = Order::where('tracking_number', 'like', 'SSP' . $currentYearMonth . '%')
            ->orderBy('id', 'desc')
            ->first();

        // Generate the unique incremental number
        $incrementalNumber = 1; // Default if no orders exist for this month
        if ($lastOrder) {
            // Extract the last incremental number from the tracking number and increment
            $lastTrackingNumber = $lastOrder->tracking_number;
            $lastIncremental = (int) substr($lastTrackingNumber, 9); // Extract numeric part after 'yyyymm_'
            $incrementalNumber = $lastIncremental + 1;
        }

        // Pad the incremental number to ensure it's 6 digits long
        $uniqueNumber = str_pad($incrementalNumber, 7, '0', STR_PAD_LEFT);

        // Generate the full tracking number
        $trackingNumber = 'SSP' . $currentYearMonth . $uniqueNumber;

        // Use DB transaction for atomicity
        DB::beginTransaction();


        $vendor = CustomerVendor::where('customer_id', $validated['user_id'])->first();
        $config_settings = ConfigSetting::find(1);

        try {
            // Store data into orders table
            $order = Order::create([
                'user_id' => $validated['user_id'],
                'order_status' => 0, // Default status
                'total_amount' => $validated['subtotal'],
                'discount' => $validated['savings'],
                'grand_total' => $validated['grand_total'],
                'tracking_number' => $trackingNumber,
                'supplied_by' => $vendor->vendor_id,
                'vendor_comission_percentage' => $config_settings->vendor_comission,
                'vendor_comission_total' => (($validated['grand_total'] / 100) * $config_settings->vendor_comission),
            ]);

            // Retrieve the created order ID
            $orderId = $order->id;

            // Prepare order items data
            $orderItems = [];
            foreach ($validated['cart_data'] as $cartItem) {
                $orderItems[] = [
                    'order_id' => $orderId,
                    'user_id' => $validated['user_id'],
                    'product_id' => $cartItem['product_id'],
                    'product_variant_id' => $cartItem['product_variant_id'],
                    'quantity' => $cartItem['quantity'],
                    'unit_quantity' => $cartItem['unit_quantity'],
                    'unit_title' => $cartItem['unit_title'],
                    'price' => $cartItem['price'],
                    'discount' => $cartItem['discount'],
                    'total_amount'  => $cartItem['quantity'] * $cartItem['price'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                // Collect the cart item id for deletion later
                $cartIds[] = $cartItem['id'];
            }

            // Insert data into order_items table
            OrderItem::insert($orderItems);

            // Delete cart items that were used in the order
            Cart::whereIn('id', $cartIds)->delete();

            // Commit the transaction
            DB::commit();

            // Return response
            return response()->json([
                'message' => 'Order placed successfully.',
                'order_id' => $orderId,
                'tracking_number' => $trackingNumber,
            ], 201);
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();

            // Return error response
            return response()->json([
                'message' => 'Failed to place order.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function getOrderByTrackingNumber($trackingNumber)
    {
        // Retrieve the order using the provided tracking number
        $order = Order::where('tracking_number', $trackingNumber)->first();

        if (!$order) {
            return response()->json([
                'message' => 'Order not found.',
            ], 404);
        }

        // Retrieve the associated order items
        $orderItems = OrderItem::where('order_id', $order->id)->get();

        // Prepare the response data
        $response = [
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'order_status' => $order->order_status,
            'total_amount' => $order->total_amount,
            'discount' => $order->discount,
            'grand_total' => $order->grand_total,
            'tracking_number' => $order->tracking_number,
            'order_items' => $orderItems->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'unit_quantity' => $item->unit_quantity,
                    'unit_title' => $item->unit_title,
                    'price' => $item->price,
                    'discount' => $item->discount,
                    'total_amount' => $item->total_amount,
                ];
            }),
        ];

        // Return the order details as a response
        return response()->json([
            'message' => 'Order details retrieved successfully.',
            'order' => $response,
        ], 200);
    }


    // getting all the orders desc on date with pagination

    public function getAllOrders(Request $request)
    {
        // Retrieve orders with pagination and order them by created_at in descending order
        $orders = Order::orderBy('created_at', 'desc')->paginate(10); // Adjust the number per page as needed

        if ($orders->isEmpty()) {
            return response()->json([
                'message' => 'No orders found.',
            ], 404);
        }

        // Map the orders to include their associated items and product image
        $response = $orders->map(function ($order) {
            // Retrieve the associated order items
            $orderItems = OrderItem::where('order_id', $order->id)->get();

            return [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'order_status' => $order->order_status,
                'total_amount' => $order->total_amount,
                'discount' => $order->discount,
                'grand_total' => $order->grand_total,
                'tracking_number' => $order->tracking_number,
                'OrderDate' => $order->created_at,
            ];
        });

        // Return the paginated orders as a response
        return response()->json([
            'message' => 'All orders retrieved successfully.',
            'orders' => $response,
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'total_pages' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total_orders' => $orders->total(),
            ],
        ], 200);
    }




    public function getOrdersByUserId(Request $request)
    {
        // Validate the request to ensure user_id is provided
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        // Retrieve the user_id from the request
        $userId = $request->user_id;

        // Retrieve all orders for the specified user
        $orders = Order::where('user_id', $userId)
                    ->orderBy('created_at', 'desc') // Sorting in descending order    
                    ->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'message' => 'No orders found for this user.',
            ], 404);
        }

        // Map the orders to include their associated items and product image
        $response = $orders->map(function ($order) {
            // Retrieve the associated order items
            $orderItems = OrderItem::where('order_id', $order->id)->get();

            return [
                'order_id' => $order->id,
                'order_status' => $order->order_status,
                'total_amount' => $order->total_amount,
                'discount' => $order->discount,
                'grand_total' => $order->grand_total,
                'tracking_number' => $order->tracking_number,
                'OrderDate' => $order->created_at,
                'order_items' => $orderItems->map(function ($item) {
                    // Retrieve the product for the current item
                    $product = Product::find($item->product_id);
                    Log::info("$item is from the orderdetails");
                    Log::info("product $product");
                    return [
                        'product_id' => $item->product_id,
                        'image_url' => $product ? $product->image_url : null, // Get image_url if product exists
                        'product_title' => $product ? $product->title : null,
                        'product_variant_id' => $item->product_variant_id,
                        'quantity' => $item->quantity,
                        'unit_quantity' => $item->unit_quantity,
                        'unit_title' => $item->unit_title,
                        'price' => $item->price,
                        'discount' => $item->discount,
                        'total_amount' => $item->total_amount,
                    ];
                }),
            ];
        });

        // Return the user orders as a response
        return response()->json([
            'message' => 'User orders retrieved successfully.',
            'orders' => $response,
        ], 200);
    }




    public function show(Order $order)
    {
        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Specified Order displayed successfully',
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/orders/{order}",
     *     tags={"Orders"},
     *     summary="Update a specific Order",
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"order_status", "total_amount"},
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="address_id", type="integer"),
     *             @OA\Property(property="order_status", type="integer"),
     *             @OA\Property(property="tracking_number", type="string"),
     *             @OA\Property(property="total_amount", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", ref="#/components/schemas/Order"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'address_id' => 'nullable|integer',
            'order_status' => 'required|integer',
            'tracking_number' => 'nullable|string',
            'total_amount' => 'required|integer',
        ]);

        $order->update($request->all());
        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Order updated successfully',
        ]);
    }

    //  update the order status by orrder id 

    public function updateOrderStatus(Request $request, Order $order)
    {
        // Validate the request to ensure order_id and status are provided
        $request->validate([
            'order_status' => 'required|Integer', // Adjust valid statuses as needed
        ]);


        $order->update(['order_status' => $request->order_status]);

        if ($request->order_status == 2) {
            $userDetail = UserDetails::where('user_id', $order->user_id)->first();
            $earningController = new EarningController();

            if ($userDetail->is_first_order_completed == 0) {
                $ordered_user_id = $userDetail->user_id;
                $referal_user_id = $userDetail->referred_by;
                $sucess = $earningController->updateEarningsWithReferralIncentive($referal_user_id, $ordered_user_id);
                if ($sucess == 'success') {
                    $userDetail->is_first_order_completed = 1;
                    $userDetail->save(); // Persist changes
                }
            }

            // // Call the addComission method
            $earningController->addComission($order->id);
        }

        // Return a success response
        return response()->json([
            'message' => 'Order status updated successfully.',
            'order' => [
                'order_id' => $order->id,
                'order_status' => $order->order_status,
            ],
        ], 200);
    }


    public function getPaidWallet(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'user_id' => 'required|integer',
        ]);

        $userId = $request->user_id;

        // Query to calculate the sum of grand_total for the given user_id
        $total = Order::where('user_id', $userId)
            ->whereIn('order_status', [0, 1]) // Check for order_status 0 or 1
            ->sum('grand_total');

        return response()->json([
            'success' => true,
            'user_id' => $userId,
            'total_grand_total' => $total,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/orders/{order}",
     *     tags={"Orders"},
     *     summary="Delete a specific Order",
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully',
        ], 200);
    }

    public function getOrderItemsForSupplier(Request $request)
    {
        Log::info("hello this is getordersItems by supplier");
        // Get the authenticated user's ID as the supplier
        $supplier = Vendor::where('user_id', $request->user_id)->first();
        $supplierId = $supplier->id;

        // Default order status filter
        $orderStatusFilter = [0, 1];

        // Override order status filter if provided in the query string
        if ($request->has('order_status')) {
            $orderStatusFilter = (array) $request->query('order_status');
        }

        // Default category filter (all categories)
        $categoryFilter = '*';
        if ($request->has('category_id')) {
            $categoryFilter = $request->query('category_id');
        }

        // Build the query
        $query = OrderItem::query()
            ->select([
                'order_items.product_variant_id',
                DB::raw('SUM(order_items.quantity) AS total_quantity'),
                'orders.supplied_by AS supplier',
                'order_items.product_id AS Product_ID',
                'order_items.unit_quantity AS Unit_Quantity',
                'orders.order_status',
                'order_items.unit_title AS Unit_title',
            ])
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.supplied_by', $supplierId)
            ->whereIn('orders.order_status', $orderStatusFilter)
            ->groupBy(
                'order_items.product_variant_id',
                'orders.supplied_by',
                'orders.order_status',
                'order_items.product_id',
                'order_items.unit_quantity',
                'order_items.unit_title'
            );

        // Apply category filter if specified
        if ($categoryFilter !== '*') {
            $query->whereHas('product', function ($productQuery) use ($categoryFilter) {
                $productQuery->where('category_id', $categoryFilter);
            });
        }

        // Execute the query
        $results = $query->get();

        // Map the product details
        $results->each(function ($item) {
            $product = Product::find($item->Product_ID);
            $item->product_title = $product->title ?? null;
            $item->image_url = $product->image_url ?? null;
            $item->category_id = $product->category_id ?? null;
        });

        return response()->json($results);
    }
}
