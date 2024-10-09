<?php
  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
  
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\BusinessController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CategoryController;
   
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
Route::put('category/{category}', [CategoryController::class, 'update'])->name('category.update');
Route::delete('category/{category}', [CategoryController::class, 'destroy'])->name('category.destroy');


});

