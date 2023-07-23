<?php

use App\Http\Controllers\API\UserContoller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', [UserContoller::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/get-all-users', [UserContoller::class, 'index']);
    Route::post('/store-users', [UserContoller::class, 'store']);
    Route::get('/show-single-user/{id}', [UserContoller::class, 'show']);
    Route::post('/udpate/{id}', [UserContoller::class, 'update']);
});
