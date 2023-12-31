<?php

use App\Http\Controllers\API\UserContoller;
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
// User login
Route::post('login', [UserContoller::class, 'login']);

// register users
Route::post('/store-users', [UserContoller::class, 'store']);

// verify otp
Route::post('/verify-otp', [UserContoller::class, 'verifyOTP']);

// resend otp
Route::post('/resend-otp', [UserContoller::class, 'resendOtp']);

// get csrf token
Route::get('/csrf-token', [UserContoller::class, 'getToken']);

Route::middleware('auth:sanctum')->group(function () {
    // get all users
    Route::get('/get-all-users', [UserContoller::class, 'index']);

    // get single user
    Route::get('/show-single-user/{id}', [UserContoller::class, 'show']);

    // update info of a user
    Route::post('/udpate/{id}', [UserContoller::class, 'update']);

    // soft deleting
    Route::delete('/delete/{id}', [UserContoller::class, 'archive']);

    // permanent deletion
    Route::delete('destroy/{id}', [UserContoller::class, 'delete']);

    // super admin creating admin account
    Route::post('/create-admin', [UserContoller::class, 'admins']);

    // super admin creating end user account
    Route::post('/create-user', [UserContoller::class, 'users']);
});
