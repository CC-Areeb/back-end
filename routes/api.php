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

// register users
Route::post('/store-users', [UserContoller::class, 'store']);
// verify otp
Route::post('/verify-otp', [UserContoller::class, 'verifyOTP']);

Route::middleware('auth:sanctum')->group(function () {
    // get all users
    Route::get('/get-all-users', [UserContoller::class, 'index']);


    // get single user
    Route::get('/show-single-user/{id}', [UserContoller::class, 'show']);

    // update info of a user
    Route::post('/udpate/{id}', [UserContoller::class, 'update']);

    // soft deleting
    Route::delete('/delete/{id}', [UserContoller::class, 'archive']);

    // full deletion
});
