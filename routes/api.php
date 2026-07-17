
<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;



Route::prefix('auth')->group(function () {
    // Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::post('profile', [AuthController::class, 'profile']);
        Route::post('logout', [AuthController::class, 'logout']);
        // Route::post('refresh', [AuthController::class, 'refresh']);
    });
});


Route::middleware(['auth:api','role:ADMIN|CASHIER|MANAGER'])
->group(function (){

    Route::post("/categories",[CategoryController::class, "store"]);
    Route::post('/products', [ProductController::class, 'store']);
    Route::post('/orders',[OrderController::class,'store']);
    Route::get('/orders',[OrderController::class, 'index']);
    Route::get('/orders/{order}',[OrderController::class, 'show']);
    Route::patch('/orders/{order}/cancel',[OrderController::class, 'cancel']);

    
});

Route::middleware(['auth:api','role:ADMIN|MANAGER'])->group(function (){
    Route::get('/reports/daily-sales',[ReportController::class,'dailySales']);

    Route::patch('/products/{product}', [ProductController::class, 'update']);
});


Route::middleware(['auth:api','role:ADMIN'])->group(function (){

    Route::post('/users',[UserController::class, 'store']);
    Route::get('/users',[UserController::class, 'index']);
    Route::get('/users/{id}',[UserController::class, 'show']);
    Route::put('/users/{user}',[UserController::class, 'update']);
    Route::delete('/users/{user}',[UserController::class,'destroy']);
    Route::patch('/users/{user}/password', [UserController::class, 'changePassword']
);
    Route::get('/dashboard',[DashboardController::class,'index']);

});


Route::get('/products', [ProductController::class, 'index']);
Route::get("/categories",[CategoryController::class, "index"]);

