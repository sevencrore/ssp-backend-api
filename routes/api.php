<?php

use App\Http\Controllers\API\BrandController;
use App\Http\Controllers\API\CityController;
use App\Http\Controllers\API\EMailController;
use App\Http\Controllers\API\VendorCommissionController;
use App\Http\Controllers\API\VendorTransactionsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\BusinessController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\UnitController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\OrderItemController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\UserDetailsController;
use App\Http\Controllers\API\ProductVariantController;
use App\Http\Controllers\API\UserReferralController;
use App\Http\Controllers\API\EarningController;
use App\Http\Controllers\API\UserBankController;
use App\Http\Controllers\API\ImageController;
use App\Http\Controllers\ComissionController;
use App\Http\Controllers\ComissionDetailController;
use App\Http\Controllers\API\ComissionHistoryController;
use App\Http\Controllers\CustomerVendorController;
use App\Http\Controllers\API\UsersController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\ConfigSettingController;
use App\Http\Controllers\API\SlideImageController;

use App\Http\Controllers\API\AddressController;
use App\Http\Controllers\API\BuyProductController;
use App\Http\Controllers\API\RazorpayPaymentController;
use App\Http\Controllers\API\ResetPasswordEmailController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\UserPaymentController;

Route::controller(RegisterController::class)->group(function () {
  Route::post('register', 'register');
  Route::post('register-referral', 'registerWthReferral'); // Correct the method name here
  Route::post('register-vendor', 'registerVendor'); // Correct the method name here
  Route::post('login', 'login');

  // Password reset routes
  Route::post('password/email/send-otp', [ResetPasswordEmailController::class, 'sendOtp']);
  Route::post('password/email/verify-otp', [ResetPasswordEmailController::class, 'verifyOtp']);
});

// Route to upload an image
Route::post('upload', [ImageController::class, 'upload'])->name('image.upload');

// Route to view all images
Route::get('images', [ImageController::class, 'index'])->name('image.index');




Route::middleware('auth:sanctum')->group(function () {
  Route::post('logout', [RegisterController::class, 'logout']);
  Route::post('/send-email', [EMailController::class, 'sendEmailAPI']);

  Route::middleware(['role:admin|vendor|user'])->group(function () {
    Route::put('/users/change-password', [UsersController::class, 'updatePasswordWithOldPassword'])->name('users.updatePasswordWithOldPassword');
  });

    Route::middleware(['role:admin|user|operator'])->group(function () {
      Route::get('products-get-all-paginated', [ProductController::class, 'getAllPaginated']);
      Route::get('products-Custom-Product-Get-All-Paginated', [ProductController::class, 'CustomProductGetAllPaginated']);
      Route::get('products-with-variants', [ProductController::class, 'getProductsWithVariants']);
      Route::get('category', [CategoryController::class, 'index'])->name('category.index');
      Route::get('orders/user-orders', [OrderController::class, 'getOrdersByUserId'])->name('orders.getOrdersByUserId');
      Route::get('orders/get-paid-wallet', [OrderController::class, 'getPaidWallet'])->name('orders.getPaidWallet');
      Route::post('address', [AddressController::class, 'store'])->name('address.store');
      Route::get('cart', [CartController::class, 'index'])->name('cart.index');
      Route::get('cart/{cart}', [CartController::class, 'show'])->name('cart.show');
      Route::put('cart/{cart}', [CartController::class, 'update'])->name('cart.update');
      Route::post('cart/user/update-cartdata', [CartController::class, 'updateCartItems'])->name('cart.updateCartItems');
      Route::delete('cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');
      Route::get('cart/user/{userId}', [CartController::class, 'getCartByUserId'])->name('cart.getCartByUserId');
      Route::get('product-variant-get-all-paginated', [ProductVariantController::class, 'getAllPaginated']);
      Route::get('product-variant-with-variants', [ProductVariantController::class, 'getProductsWithVariants']);
      Route::get('/getproductvariants_ByProductId', [ProductVariantController::class, 'getVariantsByProductId']);
      Route::get('earnings', [EarningController::class, 'index'])->name('earnings.index');
      Route::get('earnings/realsales', [EarningController::class, 'getReferralSales'])->name('earnings.getReferralSales');
      Route::get('earnings/referral/{user_id}', [EarningController::class, 'getEarningsByUser'])->name('earnings.getEarningsByUser');
      Route::get('earnings/{earning}', [EarningController::class, 'show'])->name('earnings.show');
      Route::get('/get-minimum-order', [ComissionController::class, 'getMinimumOrder']);
      Route::get('comission-history/{comissionHistory}', [ComissionHistoryController::class, 'show'])->name('comission_history.show');
      Route::get('comission-history/user/{user_id}', [ComissionHistoryController::class, 'getCommissionHistory'])->name('comission_history.getCommissionHistory');
      Route::get('/users/details', [UsersController::class, 'show'])->name('users.show');
      Route::post('user-details', [UserDetailsController::class, 'store'])->name('user-details.store');
      Route::post('cart', [CartController::class, 'store'])->name('cart.store');
      Route::get('get-user-details', [UserDetailsController::class, 'GetUserDetails'])->name('user-details.GetUserDetails');

       // razorpay routes
      Route::post('payment/create-order', [RazorpayPaymentController::class, 'createOrder'])->name('razorpay.createOrder');
      Route::post('user/BuyProduct', [BuyProductController::class, 'BuyProduct'])->name('buyproduct.BuyProduct');
      Route::get('user/get-directReferals', [UserDetailsController::class, 'getDirectReferralsDetails'])->name('userDetails.getDirectReferralsDetails');
      Route::get('user/getUser_minimum_order', [UserDetailsController::class, 'getUser_minimum_order'])->name('userDetails.getUser_minimum_order');
      Route::get('getforntpage-Images', [SlideImageController::class, 'getforntpageImages'])->name('slideimages.getforntpageImages');
    });

    Route::middleware(['role:admin|operator'])->group(function () {
      Route::get('products', [ProductController::class, 'index']);
      Route::post('products', [ProductController::class, 'store']);
      Route::get('product/{id}', [ProductController::class, 'show']);
      Route::post('products/update/{id}', [ProductController::class, 'update']);
      Route::delete('products/{id}', [ProductController::class, 'destroy']);
      Route::delete('Product/force-delete-multiple', [ProductController::class, 'forceDeleteMultiple']);

      Route::post('category', [CategoryController::class, 'store'])->name('category.store');
      Route::get('category-get-all-paginated', [CategoryController::class, 'getAllPaginated']);
      Route::get('category/{category}', [CategoryController::class, 'show'])->name('category.show');
      Route::put('category/{id}', [CategoryController::class, 'update'])->name('category.update');
      Route::post('categoryupdate/{id}', [CategoryController::class, 'updatecategory'])->name('category.updatecategory');
      Route::delete('category/{category}', [CategoryController::class, 'destroy'])->name('category.destroy');
    
      Route::get('unit', [UnitController::class, 'index'])->name('unit.index');
      Route::post('unit', [UnitController::class, 'store'])->name('unit.store');
      Route::get('unit-get-all-paginated', [UnitController::class, 'getAllPaginated']);
      Route::get('unit/{unit}', [UnitController::class, 'show'])->name('unit.show');
      Route::put('unit/{id}', [UnitController::class, 'update'])->name('unit.update');
      Route::delete('unit/{unit}', [UnitController::class, 'destroy'])->name('unit.destroy');

      Route::get('product-variant', [ProductVariantController::class, 'index'])->name('product_variant.index');
      Route::post('product-variant', [ProductVariantController::class, 'store'])->name('product_variant.store');
      Route::get('product-variant/{product_variant}', [ProductVariantController::class, 'show'])->name('product_variant.show');
      Route::put('product-variant/{id}', [ProductVariantController::class, 'update'])->name('product_variant.update');
      Route::delete('product-variant/{product_variant}', [ProductVariantController::class, 'destroy'])->name('product_variant.destroy');
      
      Route::get('slideimages', [SlideImageController::class, 'index'])->name('slideimages.index');
      Route::get('slideimages-ByFilter', [SlideImageController::class, 'getALLPaginated'])->name('slideimages.getALLPaginated');
      Route::post('slideimages', [SlideImageController::class, 'store'])->name('slideimages.store');
      Route::get('slideimages/{slideImage}', [SlideImageController::class, 'show'])->name('slideimages.show');
      Route::post('slideimages/{slideImage}', [SlideImageController::class, 'update'])->name('slideimages.update');
      Route::delete('slideimages/{slideImage}', [SlideImageController::class, 'destroy'])->name('slideimages.destroy');
      
      Route::get('/admin/getTopReferrers', [AdminController::class, 'getTopReferrers']);
      Route::get('admin/dashboard-count', [AdminController::class, 'getDashboard_count']);

    });

    Route::middleware(['role:admin'])->group(function () {
      Route::post('/roles/update', [RoleController::class, 'UpdateRole']);  // Only admin can assign roles

      Route::post('user-referrals', [UserReferralController::class, 'store'])->name('user-referrals.store');
      Route::get('user-referrals', [UserReferralController::class, 'index'])->name('user-referrals.index');
      Route::get('user-referrals/{userReferral}', [UserReferralController::class, 'show'])->name('user-referrals.show');
      Route::put('user-referrals/{userReferral}', [UserReferralController::class, 'update'])->name('user-referrals.update');
      Route::delete('user-referrals/{userReferral}', [UserReferralController::class, 'destroy'])->name('user-referrals.destroy');
      
      Route::post('earnings', [EarningController::class, 'store'])->name('earnings.store');
      Route::put('earnings/{earning}', [EarningController::class, 'update'])->name('earnings.update');
      Route::delete('earnings/{earning}', [EarningController::class, 'destroy'])->name('earnings.destroy');
      
      Route::post('comissions', [ComissionController::class, 'store'])->name('comissions.store');
      Route::get('comission/getAllPaginated', [ComissionController::class, 'getAllPaginated']);
      Route::get('comissions/{id}', [ComissionController::class, 'show'])->name('comissions.show');
      Route::put('comissions/{id}', [ComissionController::class, 'update'])->name('comissions.update');
      Route::delete('comissions/{id}', [ComissionController::class, 'destroy'])->name('comissions.destroy');
      

      Route::get('comission-details', [ComissionDetailController::class, 'index'])->name('comission_details.index');
      Route::get('comission-details/{id}', [ComissionDetailController::class, 'show'])->name('comission_details.show');
      Route::get('comission-details-getAllPaginated', [ComissionDetailController::class, 'getAllPaginated'])->name('comission_details.getAllPaginated');
      Route::post('comission-details', [ComissionDetailController::class, 'store'])->name('comission_details.store');
      Route::put('comission-details/{id}', [ComissionDetailController::class, 'update'])->name('comission_details.update');
      Route::delete('comission-details/{id}', [ComissionDetailController::class, 'destroy'])->name('comission_details.destroy');
      
      Route::get('comission-history', [ComissionHistoryController::class, 'index'])->name('comission_history.index');
      Route::post('comission-history', [ComissionHistoryController::class, 'store'])->name('comission_history.store');
      Route::put('comission-history/{comissionHistory}', [ComissionHistoryController::class, 'update'])->name('comission_history.update');
      Route::delete('comission-history/{comissionHistory}', [ComissionHistoryController::class, 'destroy'])->name('comission_history.destroy');
      
      Route::get('address/userAddress', [AddressController::class, 'GetUserAddresses'])->name('address.GetUserAddresses');
      Route::put('address/{id}', [AddressController::class, 'update'])->name('address.update');
      
      Route::post('order-items', [OrderItemController::class, 'store'])->name('order-items.store');
      Route::get('order-items', [OrderItemController::class, 'index'])->name('order-items.index');
      Route::get('order-items/{orderItem}', [OrderItemController::class, 'show'])->name('order-items.show');
      Route::put('order-items/{orderItem}', [OrderItemController::class, 'update'])->name('order-items.update');
      Route::delete('order-items/{orderItem}', [OrderItemController::class, 'destroy'])->name('order-items.destroy');
      
      Route::get('user-details', [UserDetailsController::class, 'index'])->name('user-details.index');
      Route::get('user-details/{userDetail}', [UserDetailsController::class, 'show'])->name('user-details.show');
      Route::put('user-details/{userDetail}', [UserDetailsController::class, 'update'])->name('user-details.update');
      Route::delete('user-details/{userDetail}', [UserDetailsController::class, 'destroy'])->name('user-details.destroy');

      Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
      Route::post('orders/create', [OrderController::class, 'storeOrder'])->name('orders.storeOrder');
      Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
      Route::get('orders/track/{tracking_number}', [OrderController::class, 'getOrderByTrackingNumber'])->name('orders.getOrderByTrackingNumber');
      // Route for retrieving user paid walletamount for orders which are in orderd or dispatched state showing pending amount goods is coming 
      Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');
      Route::put('orders/status/{order}', [OrderController::class, 'updateOrderStatus'])->name('orders.updateOrderStatus');
      Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

      // Store a new customer-vendor relationship
      Route::post('customer-vendors', [CustomerVendorController::class, 'store']);
      // Get all customer-vendor relationships
      Route::get('customer-vendors', [CustomerVendorController::class, 'index']);
      // Delete a specific customer-vendor relationship
      Route::delete('customer-vendors/{id}', [CustomerVendorController::class, 'destroy']);
      Route::put('/users/edit', [UsersController::class, 'update'])->name('users.update');
    
      // Get users with cold_state = 1 and filters
      Route::get('users/cold-state', [UsersController::class, 'getAllColdStateUsers'])->name('users.getAllColdStateUsers');
      Route::get('vendors', [VendorController::class, 'index']); // Get all vendors
      Route::get('admin/vendors', [VendorController::class, 'getAllPaginated']); // Get all vendors
      Route::get('vendors/{id}', [VendorController::class, 'show']); // Get a specific vendor by ID
      Route::put('vendors/{id}', [VendorController::class, 'update']); // Update a specific vendor by ID
      Route::delete('vendors/{id}', [VendorController::class, 'destroy']); // Delete a specific vendor by ID
      
      Route::get('/get-users', [UsersController::class, 'getUsersBySearch'])->name('users.getUsersBySearch');
      Route::get('/get-user/userId', [UsersController::class, 'getUserDetailsByUserId'])->name('users.getUserDetailsByUserId');
      
      Route::post('admin/createUsers', [RegisterController::class, 'CreateUserByAdmin'])->name('register.CreateUserByAdmin');
      Route::get('admin/getAdminUsers', [AdminController::class, 'getAdminUsersList'])->name('admin.getAdminUsersList');
      Route::post('/admin/users/updatepassword', [AdminController::class, 'updatePasswordByAdmin'])->name('admin.updatePasswordByAdmin');
      Route::get('orders/admin', [OrderController::class, 'getAllOrders'])->name('orders.getAllOrders');
      Route::get('order-items/admin/{orderItem}', [OrderItemController::class, 'getOrderItemsByOrderId'])->name('order-items.getOrderItemsByOrderId');
      Route::put('/admin/update-user-password', [AdminController::class, 'updatePassword']);
      Route::put('/admin/update-user-address', [AdminController::class, 'updateUserAddressByAdmin']);
      Route::put('/admin/update-user-state/{id}', [AdminController::class, 'setStatus']);
      Route::get('admin/Ordertransactions', [UserPaymentController::class, 'getAdminproduct_transactions'])->name('userPayment.getAdminproduct_transactions');
      Route::get('admin/getUserDetailsByEmail', [UserDetailsController::class, 'getUserDetailsByEmail']);
      Route::post('/admin/updateVendor', [CustomerVendorController::class, 'updateVendor']); //update the customer vendor mapping
      Route::get('/admin/getOrderDetails', [OrderController::class, 'getOrderDetails']);
      Route::get('/admin/getSpecificOrderDetails', [OrderController::class, 'getSpecificOrderDetails']);
      Route::get('/admin/getspecific/VendorComission', [VendorCommissionController::class, 'getVendorCommission_Admin']);
      Route::get('/admin/getAll/VendorComission', [VendorCommissionController::class, 'fetchAdminVendorCommissions']);
      
      Route::get('/config-settings', [ConfigSettingController::class, 'index']);
      Route::get('/config-settings/{id}', [ConfigSettingController::class, 'show']);
      Route::post('/config-settings', [ConfigSettingController::class, 'store']);
      Route::put('/config-settings/{id}', [ConfigSettingController::class, 'update']);
      
      Route::post('vendor-commissions/status-update', [VendorCommissionController::class, 'update_status']);
      Route::get('vendor-unpaid-commissions', [VendorCommissionController::class, 'getUnpaid_VendorCommission_list']);
      Route::post('vendor-transactions', [VendorTransactionsController::class, 'store']);
    });
   
    // admin vendor supplier routes
    Route::middleware(['role:admin'])->group(function () {
      Route::get('orders/get-supplier-orders', [OrderController::class, 'getOrderItemsForSupplier'])->name('orders.getOrderItemsForSupplier');
    });

    Route::middleware(['role:admin|vendor'])->group(function () {
      Route::get('orders/supplier', [OrderController::class, 'getAllsupplierOrders'])->name('orders.getAllsupplierOrders');
      Route::Post('orders/supplier/delivery-otp/{order}', [OrderController::class, 'DeliveryOTP'])->name('orders.DeliveryOTP');
      Route::get('get-vendor-transactions', [VendorTransactionsController::class, 'getVendorTransactions_WithPagination']);
      Route::get('get-vendor-commissions', [VendorCommissionController::class, 'getVendorCommission_WithPagination']);
    });

  });
  
  // Route to get all records
  Route::get('comissions', [ComissionController::class, 'index'])->name('comissions.index');
  
  // admin vendor operator routes
  //Route::get('orders/get-supplier-orders', [OrderController::class, 'getOrderItemsForSupplier'])->name('orders.getOrderItemsForSupplier');
 
  