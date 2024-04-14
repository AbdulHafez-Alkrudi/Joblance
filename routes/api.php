<?php

use App\Http\Controllers\{
    Auth\LoginController,
    Auth\LogoutController,
    Auth\RegisterController,
    Auth\GoogleLoginController,
    CompanyController,
    ConversationController,
    EmailVerificationController,
    FreelancerController,
    MessageController,
    NotificationController,
    ResetCodePasswordController,
    UserController,
};
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

// for email verification
Route::post('user/email/code/check', [EmailVerificationController::class, 'userCheckCode']);
Route::post('user/email/code/resend', [EmailVerificationController::class, 'userResendCode']);

// for google login
Route::post('auth/google/login', [GoogleLoginController::class, 'googleLogin']);
Route::post('auth/google/userinfo', [GoogleLoginController::class, 'getUserINfo']);

// for Authentication
Route::post('register' , RegisterController::class);
Route::post('login', [LoginController::class , 'login']);

Route::middleware(['auth:api']) ->group(function(){
    Route::post('user/logout', LogoutController::class)->name('logout');
    Route::get('user/profile/{id}', [UserController::class, 'show'])->name('profile');
    Route::post('user/changepassword', [UserController::class, 'changePassword'])->name('changePassword');

    // for Notifications
    Route::post('user/mynotifications', [NotificationController::class, 'myNotifications'])->name('myNotifications');
    Route::post('user/newnotifications', [NotificationController::class, 'newNotifications'])->name('newNotifications');

    // for Conversations
    Route::get('conversations', [ConversationController::class, 'index']);
    Route::get('conversations/{id}', [ConversationController::class, 'show']);
    Route::post('conversations/addparticipant', [ConversationController::class, 'addParticipant']);
    Route::delete('conversations/removeparticipant', [ConversationController::class, 'removeParticipant']);
    Route::put('conversations/{id}/read', [ConversationController::class, 'markAsRead']);
    Route::delete('conversations/delete/{id}', [ConversationController::class, 'destroy']);

    // for Messages
    Route::get('conversations/{id}/messages', [MessageController::class, 'getMessages']);
    Route::post('message/send', [MessageController::class, 'sendMessage']);
    Route::delete('message/{id}/delete', [MessageController::class, 'deleteMessage']);

    Route::middleware(['auth:api', 'can:isCompany']) ->group(function(){

    });

    Route::middleware(['auth:api', 'can:isFreelancer']) ->group(function(){

    });
});
