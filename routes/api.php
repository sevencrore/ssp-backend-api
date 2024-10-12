<?php
  
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



use App\Http\Controllers\API\AddressController;
   
Route::controller(RegisterController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
});
         
Route::middleware('auth:sanctum')->group( function () {
    
    // Example in api.php
//Route::get('/bussiness', [BussinessController::class, 'index'])->name('bussiness.paginate');
// Define route for displaying a list of products
Route::get('business', [BusinessController::class, 'index'])->name('business.index');

// Define route for showing the form to create a new product
Route::get('business/create', [BusinessController::class, 'create'])->name('business.create');

// Define route for storing a newly created product
Route::post('business', [BusinessController::class, 'store'])->name('business.store');

// Define route for displaying a specific product
Route::get('business/{business}', [BusinessController::class, 'show'])->name('business.show');

// Define route for showing the form to edit a specific product
Route::get('business/{business}/edit', [BusinessController::class, 'edit'])->name('business.edit');
Route::post('business/{business}/edit', [BusinessController::class, 'edit'])->name('business.edit');

// Define route for updating a specific product
Route::put('business/{business}', [BusinessController::class, 'update'])->name('business.update');

// Define route for deleting a specific product
//Route::delete('business/{business}', [BusinessController::class, 'destroy'])->name('business.destroy');



//Route::delete('business/{business}', [BusinessController::class, 'destroy'])->name('business.destroy');
// Route::post('business/{business}/restore', [BusinessController::class, 'restore'])->name('business.restore');
// Route::delete('business/{business}/force-delete', [BusinessController::class, 'forceDelete'])->name('business.forceDelete');
// Route::get('business/trashed', [BusinessController::class, 'trashed'])->name('business.trashed');

Route::delete('business/delete-multiple', [BusinessController::class, 'deleteMultiple']);
Route::post('business/restore-multiple', [BusinessController::class, 'restoreMultiple']);
Route::delete('business/force-delete-multiple', [BusinessController::class, 'forceDeleteMultiple']);
Route::post('business/trashed-multiple', [BusinessController::class, 'trashedMultiple']);


Route::get('products', [ProductController::class, 'index']);
Route::get('products-get-all-paginated', [ProductController::class, 'getAllPaginated']);
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




// Route for creating a new order
Route::post('orders', [OrderController::class, 'store'])->name('orders.store');

// Route for retrieving all orders
Route::get('orders', [OrderController::class, 'index'])->name('orders.index');

// Route for retrieving a specific order
Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');

// Route for updating a specific order
Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');

// Route for deleting a specific order
Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');



Route::get('address', [AddressController::class, 'index'])->name('address.index');
Route::post('address', [AddressController::class, 'store'])->name('address.store');
Route::get('address-get-all-paginated', [AddressController::class, 'getAllPaginated']);
Route::get('address/{address}', [AddressController::class, 'show'])->name('address.show');
Route::put('address/{id}', [AddressController::class, 'update'])->name('address.update');
Route::delete('address/{address}', [AddressController::class, 'destroy'])->name('address.destroy');



// Route for creating a new order item
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


});









