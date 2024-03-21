<?php

use App\Http\Controllers\{
    Auth\LoginController,
    Auth\LogoutController,
    Auth\RegisterController,
    ResetCodePasswordController,
    GoogleLoginController,
};
use App\Models\Role;
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


Route::get('contests', function ($id) {
    return view('welcome');
});

// for reset password
Route::post('user/password/email', [ResetCodePasswordController::class, 'userForgotPassword']);
Route::post('user/password/email/resend', [ResetCodePasswordController::class, 'userResendCode']);
Route::post('user/password/code/check', [ResetCodePasswordController::class, 'userCheckCode']);
Route::post('user/password/reset', [ResetCodePasswordController::class, 'userResetPassword']);

// for google login
Route::get('/google/redirect', [GoogleLoginController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/google/callback', [GoogleLoginController::class, 'handleGoogleCallback'])->name('google.callback');

// for Authentication
Route::post('register' , RegisterController::class);
Route::post('login', [LoginController::class , 'login']);

Route::middleware(['auth:api']) ->group(function(){
    Route::post('logout', LogoutController::class);
});

