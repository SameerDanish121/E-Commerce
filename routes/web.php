<?php
use App\Http\Controllers\AdminChatController;
use App\Http\Controllers\ChatBotController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoTaskController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TestController;



Route::post('/signup', [AuthController::class, 'signup'])->name('signup');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/admin/update-profile/{id}', [AdminController::class, 'updateAdminProfile']);
Route::prefix('admin')->name('Admin.')->group(function () {
    Route::get('/{any}', [AdminController::class, 'index'])->where('any', '.*');
    ;
});
Route::prefix('customer')->name('Customer.')->group(function () {
    Route::get('/{any}', [CustomerController::class, 'index'])->where('any', '.*');
});

//-----------------------------API-ROUTES


//CUSTOMER
Route::get('/products', [AuthController::class, 'getAllProducts'])->name('products');
Route::post('/update/user/{id}', [CustomerController::class, 'updateCustomerProfile']);
Route::post('/place-order', [AuthController::class, 'placeOrder']);
Route::get('/customer-orders/{customer_id}', [AuthController::class, 'getCustomerOrders']);


//ADMIN


Route::get('/Getproducts', [AdminController::class, 'getAllProducts']);
Route::post('/product/add', [AdminController::class, 'addProduct']);
Route::post('/product/update/{id}', [AdminController::class, 'updateProduct']);
Route::post('/product/toggle-status/{id}', [AdminController::class, 'toggleProductStatus']);
Route::post('/product/restock/{id}', [AdminController::class, 'restockProduct']);
Route::get('/orders/details', [AdminController::class, 'getAllOrdersWithDetails']);
Route::get('/orders/details/{id}', [AdminController::class, 'getOrderDetails']);
Route::post('/process-order/{orderId}', [AdminController::class, 'processOrder']);
Route::get('/all-customers', [CustomerController::class, 'allCustomer']);
Route::get('/admins', [AdminController::class, 'getAllAdmins']);
Route::post('/admins', [AdminController::class, 'addAdmin']);
Route::put('/admins/{id}', [AdminController::class, 'updateAdmin']);
Route::delete('/admins/{id}', [AdminController::class, 'deleteAdmin']);

//PERMISSION PANEL
Route::get('/permissions', [AdminController::class, 'getAllPermissions']);
Route::post('/permissions', [AdminController::class, 'createPermission']);
Route::put('/permissions/{id}', [AdminController::class, 'updatePermission']);
Route::delete('/permissions/{id}', [AdminController::class, 'deletePermission']);

//ADMIN PERMISSIONS
Route::post('/assign-permission', [AdminController::class, 'assignPermissionToAdmin']);
Route::post('/remove-permission', [AdminController::class, 'removePermissionFromAdmin']);


use App\Http\Controllers\StripePaymentController;
use Illuminate\Support\Facades\Http;

Route::get('/stripe', [StripePaymentController::class, 'showForm'])->name('stripe.form');
Route::post('/stripe', [StripePaymentController::class, 'makePayment'])->name('stripe.post');

Route::post('/stripe/payment', [StripePaymentController::class, 'handlePayment']);
Route::get('/payment/return', [StripePaymentController::class, 'paymentReturn'])->name('payment.return');
Route::post('/googlepay/intent', [StripePaymentController::class, 'createGooglePayIntent'])->name('stripe.googlepay.intent');




Route::get('/order-details/{id}', [AdminController::class, 'showOrderDetails'])
     ->name('order.details');

Route::get('/hello', function () {
    return view('test_1');
});
Route::get('/hmm', function () {
    return view('new');
});
Route::get('/hello_g', function () {
    return view('test_2');
});
Route::get('/test', function () {
    return 'Hello World';
});
Route::get('/{vue_capture?}', function () {
    return view('welcome');
})->where('vue_capture', '[\/\w\.-]*')->name('sample');





