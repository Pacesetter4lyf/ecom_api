<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::match(["POST", "GET"], "/homepage", [ProductController::class, "homepage"]);
Route::match(["POST", "GET"], "/products", [ProductController::class, "products"]);
Route::match(["GET", "POST"], "/singleproduct/{id}", [ProductController::class, "single_product"]);

Route::match(["GET"], "/blogs", [ProductController::class, "blogs"]);
Route::match(["GET"], "/singleblog/{id}", [ProductController::class, "singleblog"]);

Route::match(["POST"], "/add_question", [ProductController::class, "add_question"]);
Route::match(["GET"], "/faqs", [ProductController::class, "faqs"]);


Route::match(["POST", "GET"], "/greet", [ProductController::class, "greet"]);


Route::match(['get', 'post'], '/register', [UserController::class, 'register'])->name('register');
Route::match(['get', 'post'], '/login', [UserController::class, 'authenticate'])->name('login');



Route::match(["POST", "GET"], 'save_contact', [ProductController::class, 'save_contact']);
Route::get('check_contact', [ProductController::class, 'check_contact']);
Route::post('payment', [ProductController::class, 'payment']);
Route::post('updatepayment', [ProductController::class, 'updatepayment']);

// Route::post('register', 'UserController@register');
// Route::post('login', 'UserController@authenticate');

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('user', [UserController::class, 'getAuthenticatedUser']);
    Route::put('user_favourite', [ProductController::class, 'user_favourite']);
});
