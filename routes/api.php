<?php
  
  use App\Http\Controllers\API\BrandController;
use App\Http\Controllers\API\CityController;
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
   
Route::controller(RegisterController::class)->group(function() {
  Route::post('register', 'register');
  Route::post('register-referral', 'registerWthReferral'); // Correct the method name here
  Route::post('register-vendor', 'registerVendor'); // Correct the method name here
  Route::post('login', 'login');
});

// Route to upload an image
Route::post('upload', [ImageController::class, 'upload'])->name('image.upload');

// Route to view all images
Route::get('images', [ImageController::class, 'index'])->name('image.index');



         
Route::middleware('auth:sanctum')->group( function () {
  Route::post('logout', [RegisterController::class, 'logout']);
  Route::get('business', [BusinessController::class, 'index'])->name('business.index');
  Route::get('business/create', [BusinessController::class, 'create'])->name('business.create');
  Route::post('business', [BusinessController::class, 'store'])->name('business.store');
  Route::get('business/{business}', [BusinessController::class, 'show'])->name('business.show');
  // Route::get('business/{business}/edit', [BusinessController::class, 'edit'])->name('business.edit');
  // Route::post('business/{business}/edit', [BusinessController::class, 'edit'])->name('business.edit');
  Route::put('business/{business}', [BusinessController::class, 'update'])->name('business.update');
  Route::post('business/delete-multiple', [BusinessController::class, 'deleteMultiple']);
  Route::post('business/restore-multiple', [BusinessController::class, 'restoreMultiple']);
  Route::post('business/force-delete-multiple', [BusinessController::class, 'forceDeleteMultiple']);
  Route::post('business/trashed-multiple', [BusinessController::class, 'trashedMultiple']);
  Route::get('products', [ProductController::class, 'index']);
  Route::get('products-get-all-paginated', [ProductController::class, 'getAllPaginated']);
  Route::get('products-Custom-Product-Get-All-Paginated', [ProductController::class, 'CustomProductGetAllPaginated']);
  Route::get('products-with-variants', [ProductController::class, 'getProductsWithVariants']);
  Route::post('products', [ProductController::class, 'store']);
  Route::get('product/{id}', [ProductController::class, 'show']);
  Route::put('products/{id}', [ProductController::class, 'update']);
  Route::delete('products/{id}', [ProductController::class, 'destroy']);
  Route::delete('Product/force-delete-multiple', [ProductController::class, 'forceDeleteMultiple']);
  Route::get('category', [CategoryController::class, 'index'])->name('category.index');
  Route::post('category', [CategoryController::class, 'store'])->name('category.store');
  Route::get('category-get-all-paginated', [CategoryController::class, 'getAllPaginated']);
  Route::get('category/{category}', [CategoryController::class, 'show'])->name('category.show');
  Route::put('category/{id}', [CategoryController::class, 'update'])->name('category.update');
  Route::delete('category/{category}', [CategoryController::class, 'destroy'])->name('category.destroy');
  Route::get('unit', [UnitController::class, 'index'])->name('unit.index');
  Route::post('unit', [UnitController::class, 'store'])->name('unit.store');
  Route::get('unit-get-all-paginated', [UnitController::class, 'getAllPaginated']);
  Route::get('unit/{unit}', [UnitController::class, 'show'])->name('unit.show');
  Route::put('unit/{id}', [UnitController::class, 'update'])->name('unit.update');
  Route::delete('unit/{unit}', [UnitController::class, 'destroy'])->name('unit.destroy');
  Route::get('city', [CityController::class, 'index'])->name('city.index');
  Route::post('city', [CityController::class, 'store'])->name('city.store');
  Route::get('city-get-all-paginated', [CityController::class, 'getAllPaginated']);
  Route::get('city/{city}', [CityController::class, 'show'])->name('city.show');
  Route::put('city/{id}', action: [CityController::class, 'update'])->name('city.update');
  Route::delete('city/{city}', [CityController::class, 'destroy'])->name('city.destroy');
  Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
  Route::post('orders/create', [OrderController::class, 'storeOrder'])->name('orders.storeOrder');
  // Route for retrieving all orders
  Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
  // Route for retrieving all orders off he user  by user_id
  Route::get('orders/user-orders', [OrderController::class, 'getOrdersByUserId'])->name('orders.getOrdersByUserId');
  // Route for retrieving a specific tracking number
  Route::get('orders/track/{tracking_number}', [OrderController::class, 'getOrderByTrackingNumber'])->name('orders.getOrderByTrackingNumber');
  // Route for retrieving user paid walletamount for orders which are in orderd or dispatched state showing pending amount goods is coming 
  Route::get('orders/get-paid-wallet', [OrderController::class, 'getPaidWallet'])->name('orders.getPaidWallet');
  // Route for retrieving vendors wise order items details 
  Route::get('orders/get-supplier-orders', [OrderController::class, 'getOrderItemsForSupplier'])->name('orders.getOrderItemsForSupplier');
  // Route for updating a specific order
  Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');
  // Route for updating a specific order
  Route::put('orders/status/{order}', [OrderController::class, 'updateOrderStatus'])->name('orders.updateOrderStatus');
  // Route for deleting a specific order
  Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
  // Route::get('address', [AddressController::class, 'index'])->name('address.index');
  Route::post('address', [AddressController::class, 'store'])->name('address.store');
  //Route::get('address-get-all-paginated', [AddressController::class, 'getAllPaginated']);
  // Route::get('address/{address}', [AddressController::class, 'show'])->name('address.show');
  //Route::get('address/get-user-address', [AddressController::class, 'getAddressesByUserId'])->name('address.getAddressesByUserId');
  // Route::get('address/user-address', [AddressController::class, 'getUserAddressesById'])->name('address.getUserAddressesById');
  Route::get('address/userAddress', [AddressController::class, 'GetUserAddresses'])->name('address.GetUserAddresses');
  // Route::delete('address/{address}', [AddressController::class, 'destroy'])->name('address.destroy');
  // Route for creating a new order item

  Route::put('address/{id}', [AddressController::class, 'update'])->name('address.update');

  Route::post('order-items', [OrderItemController::class, 'store'])->name('order-items.store');
  // Route for retrieving all order items
  Route::get('order-items', [OrderItemController::class, 'index'])->name('order-items.index');
  // Route for retrieving a specific order item
  Route::get('order-items/{orderItem}', [OrderItemController::class, 'show'])->name('order-items.show');
  // Route for updating a specific order item
  Route::put('order-items/{orderItem}', [OrderItemController::class, 'update'])->name('order-items.update');
  // Route for deleting a specific order item
  Route::delete('order-items/{orderItem}', [OrderItemController::class, 'destroy'])->name('order-items.destroy');
  // Route for creating a new cart item
  Route::post('cart', [CartController::class, 'store'])->name('cart.store');

  // Route for retrieving all cart items
  Route::get('cart', [CartController::class, 'index'])->name('cart.index');

  // Route for retrieving a specific cart item
  Route::get('cart/{cart}', [CartController::class, 'show'])->name('cart.show');

  // Route for updating a specific cart item
  Route::put('cart/{cart}', [CartController::class, 'update'])->name('cart.update');

  // Route for deleting a specific cart item
  Route::delete('cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');

  // Route for getting cart details by user id
  Route::get('cart/user/{userId}', [CartController::class, 'getCartByUserId'])->name('cart.getCartByUserId');

  // Route to get all user details
  Route::get('user-details', [UserDetailsController::class, 'index'])->name('user-details.index');

  // Route to store new user details
  Route::post('user-details', [UserDetailsController::class, 'store'])->name('user-details.store');

  // Route to get a specific user detail
  Route::get('user-details/{userDetail}', [UserDetailsController::class, 'show'])->name('user-details.show');

  // Route to update a specific user detail
  Route::put('user-details/{userDetail}', [UserDetailsController::class, 'update'])->name('user-details.update');

  // Route to delete a specific user detail
  Route::delete('user-details/{userDetail}', [UserDetailsController::class, 'destroy'])->name('user-details.destroy');

  Route::get('product-variant', [ProductVariantController::class, 'index'])->name('product_variant.index');
  Route::post('product-variant', [ProductVariantController::class, 'store'])->name('product_variant.store');
  Route::get('product-variant-get-all-paginated', [ProductVariantController::class, 'getAllPaginated']);
  Route::get('product-variant-with-variants', [ProductVariantController::class, 'getProductsWithVariants']);
  Route::get('product-variant/{product_variant}', [ProductVariantController::class, 'show'])->name('product_variant.show');
  Route::put('product-variant/{id}', [ProductVariantController::class, 'update'])->name('product_variant.update');
  Route::delete('product-variant/{product_variant}', [ProductVariantController::class, 'destroy'])->name('product_variant.destroy');


  // Route for creating a new user referral
  Route::post('user-referrals', [UserReferralController::class, 'store'])->name('user-referrals.store');

  // Route for retrieving all user referrals
  Route::get('user-referrals', [UserReferralController::class, 'index'])->name('user-referrals.index');

  // Route for retrieving a specific user referral
  Route::get('user-referrals/{userReferral}', [UserReferralController::class, 'show'])->name('user-referrals.show');

  // Route for updating a specific user referral
  Route::put('user-referrals/{userReferral}', [UserReferralController::class, 'update'])->name('user-referrals.update');

  // Route for deleting a specific user referral
  Route::delete('user-referrals/{userReferral}', [UserReferralController::class, 'destroy'])->name('user-referrals.destroy');



  // Route for creating a new earning
  Route::post('earnings', [EarningController::class, 'store'])->name('earnings.store');

  // Route for retrieving all earnings
  Route::get('earnings', [EarningController::class, 'index'])->name('earnings.index');
 
  // Route for retrieving the real sales value
  Route::get('earnings/realsales', [EarningController::class, 'getReferralSales'])->name('earnings.getReferralSales');

  // Route for retrieving paginated earnings
  Route::get('earnings/paginated', [EarningController::class, 'getAllPaginated'])->name('earnings.paginated');

  //Route for retreiving earning by referral
  Route::get('earnings/referral/{user_id}', [EarningController::class, 'getEarningsByUser'])->name('earnings.getEarningsByUser');

  // Route for retrieving a specific earning
  Route::get('earnings/{earning}', [EarningController::class, 'show'])->name('earnings.show');

  // Route for updating a specific earning
  Route::put('earnings/{earning}', [EarningController::class, 'update'])->name('earnings.update');

  // Route for deleting a specific earning
  Route::delete('earnings/{earning}', [EarningController::class, 'destroy'])->name('earnings.destroy');


  // Route updatting the wallet amountt whhen the purchase occcurs
  Route::post('earnings/comission/{id}', [EarningController::class, 'addcomission'])->name('earnings.addcomission');


  Route::get('user-bank', [UserBankController::class, 'index'])->name('user-bank.index');
  Route::post('user-bank', [UserBankController::class, 'store'])->name('user-bank.store');
  Route::get('user-bank/{id}', [UserBankController::class, 'show'])->name('user-bank.show');
  Route::put('user-bank/{id}', [UserBankController::class, 'update'])->name('user-bank.update');
  Route::delete('user-bank/{id}', [UserBankController::class, 'destroy'])->name('user-bank.destroy');

  Route::get('brand', [BrandController::class, 'index'])->name('brand.index');
  Route::post('brand', [BrandController::class, 'store'])->name('brand.store');
  Route::get('brand-get-all-paginated', [BrandController::class, 'getAllPaginated']);
  Route::get('brand/{brand}', [BrandController::class, 'show'])->name('brand.show');
  Route::post('brand/{id}', [BrandController::class, 'update'])->name('brand.update');
  Route::delete('brand/{brand}', [BrandController::class, 'destroy'])->name('brand.destroy');

  // Route to get a single record
  Route::get('comissions/{id}', [ComissionController::class, 'show'])->name('comissions.show');

  Route::get('/get-minimum-order', [ComissionController::class, 'getMinimumOrder']);

  // Route to update an existing record
  Route::put('comissions/{id}', [ComissionController::class, 'update'])->name('comissions.update');

  // Route to delete a record
  Route::delete('comissions/{id}', [ComissionController::class, 'destroy'])->name('comissions.destroy');

  // Route to get a single record
  Route::get('comission-details/{id}', [ComissionDetailController::class, 'show'])->name('comission_details.show');

  // Route to create a new record
  Route::post('comission-details', [ComissionDetailController::class, 'store'])->name('comission_details.store');

  // Route to update an existing record
  Route::put('comission-details/{id}', [ComissionDetailController::class, 'update'])->name('comission_details.update');

  // Route to delete a record
  Route::delete('comission-details/{id}', [ComissionDetailController::class, 'destroy'])->name('comission_details.destroy');

  // Route to get a single record by ID
  Route::get('comission-history/{comissionHistory}', [ComissionHistoryController::class, 'show'])->name('comission_history.show');

  // Route to get all records
  Route::get('comission-history', [ComissionHistoryController::class, 'index'])->name('comission_history.index');

  // Route to get all the records based on the userId
  Route::get('comission-history/user/{user_id}', [ComissionHistoryController::class, 'getCommissionHistory'])->name('comission_history.getCommissionHistory');

   // Route to get all the records based on the userId
   Route::get('prabhu', [ComissionHistoryController::class, 'name'])->name('comission_history.name');

  // Route to create a new record
  Route::post('comission-history', [ComissionHistoryController::class, 'store'])->name('comission_history.store');

  // Route to update an existing record by ID
  Route::put('comission-history/{comissionHistory}', [ComissionHistoryController::class, 'update'])->name('comission_history.update');

  // Route to delete a record by ID
  Route::delete('comission-history/{comissionHistory}', [ComissionHistoryController::class, 'destroy'])->name('comission_history.destroy');


  // Store a new customer-vendor relationship
  Route::post('customer-vendors', [CustomerVendorController::class, 'store']);

  // Get all customer-vendor relationships
  Route::get('customer-vendors', [CustomerVendorController::class, 'index']);

  // Delete a specific customer-vendor relationship
  Route::delete('customer-vendors/{id}', [CustomerVendorController::class, 'destroy']);

  // Route::get('/users', [UsersController::class, 'index'])->name('users.index');

   Route::get('/users/details', [UsersController::class, 'show'])->name('users.show');

  // Route::post('/users', [UsersController::class, 'store'])->name('users.store');

   Route::put('/users/edit', [UsersController::class, 'update'])->name('users.update');
   
   Route::put('/users/change-password', [UsersController::class, 'updatePasswordWithOldPassword'])->name('users.updatePasswordWithOldPassword');

  // Route::delete('/users/{id}', [UsersController::class, 'destroy'])->name('users.destroy');

  // Change user state
  //Route::put('/users/change-state/{user_id}/{state}', [UsersController::class, 'changeUserState'])->name('users.changeState');

  // Get users with cold_state = 1 and filters
  Route::get('users/cold-state', [UsersController::class, 'getAllColdStateUsers'])->name('users.getAllColdStateUsers');
  // Route::get('/users/cold-state', [UsersController::class, 'getAllColdStateUsers']);


  Route::get('vendors', [VendorController::class, 'index']); // Get all vendors                                                                                                                     
  
    Route::post('vendors', [VendorController::class, 'store']); // Create a new vendor
    Route::get('vendors/{id}', [VendorController::class, 'show']); // Get a specific vendor by ID
    Route::put('vendors/{id}', [VendorController::class, 'update']); // Update a specific vendor by ID
    Route::delete('vendors/{id}', [VendorController::class, 'destroy']); // Delete a specific vendor by ID

// supplier routes 
  // to get the suplier wise orders
  Route::get('orders/supplier', [OrderController::class, 'getAllsupplierOrders'])->name('orders.getAllsupplierOrders');
  
  Route::Post('orders/supplier/delivery-otp/{order}', [OrderController::class, 'DeliveryOTP'])->name('orders.DeliveryOTP');

  //  Admin Routes 
  // Route for retrieving all orders off he user  by user_id
  Route::get('orders/admin', [OrderController::class, 'getAllOrders'])->name('orders.getAllOrders');
  // Route for retrieving a specific order item
  Route::get('order-items/admin/{orderItem}', [OrderItemController::class, 'getOrderItemsByOrderId'])->name('order-items.getOrderItemsByOrderId');

  Route::put('/admin/update-user-password', [AdminController::class, 'updatePassword']);

    // Route to fetch all ConfigSettings
  Route::get('/config-settings', [ConfigSettingController::class, 'index']); 

  // Route to fetch a single ConfigSetting by ID
  Route::get('/config-settings/{id}', [ConfigSettingController::class, 'show']); 

  // Route to create a new ConfigSetting
  Route::post('/config-settings', [ConfigSettingController::class, 'store']); 

  // Route to update an existing ConfigSetting by ID
  Route::put('/config-settings/{id}', [ConfigSettingController::class, 'update']);

    // Route to display all images
  Route::get('slideimages', [SlideImageController::class, 'index'])->name('slideimages.index');

  // Route to store a new image
  Route::post('slideimages', [SlideImageController::class, 'store'])->name('slideimages.store');

  // Route to show a specific image
  Route::get('slideimages/{slideImage}', [SlideImageController::class, 'show'])->name('slideimages.show');

  // Route to update an existing image
  Route::put('slideimages/{slideImage}', [SlideImageController::class, 'update'])->name('slideimages.update');

  // Route to delete an image
  Route::delete('slideimages/{slideImage}', [SlideImageController::class, 'destroy'])->name('slideimages.destroy');

});


// Route to get all records
Route::get('comissions', [ComissionController::class, 'index'])->name('comissions.index');

// Route to get all records
Route::get('comission-details', [ComissionDetailController::class, 'index'])->name('comission_details.index');
// Route to create a new record
Route::post('comissions', [ComissionController::class, 'store'])->name('comissions.store');