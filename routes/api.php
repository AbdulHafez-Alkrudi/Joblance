<?php

use App\Models\Freelancer;
use App\Http\Controllers\{Auth\EmailVerificationController,
    Auth\GoogleLoginController,
    Auth\LoginController,
    Auth\LogoutController,
    Auth\RegisterController,
    Auth\ResetCodePasswordController,
    Chat\ConversationController,
    Chat\MessageController,
    CVController,
    DocumentAIController,
    Notification\NotificationController,
    Payment\PayPalController,
    Report\ReportController,
    Users\Freelancer\Freelancer_project\UserProjectController,
    Users\Freelancer\Freelancer_project\UserSkillsController,
    Users\Freelancer\FreelancerController,
    Users\Freelancer\SkillController,
    Users\MajorController,
    Users\UserController};


use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;


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
Broadcast::routes(['middlewere'=>['auth:api']]);


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
    Route::post('user/logout' , LogoutController::class)->name('logout');

  /*  Route::resource('user'        , UserController::class);
    Route::resource('major'       , MajorController::class);
    Route::resource('userProject' , UserProjectController::class);*/
    Route::apiResources([
        'user'  => UserController::class,
        'major' => MajorController::class,
        'userProject' => UserProjectController::class,
        'skill' => SkillController::class,
        'user_skills' => UserSkillsController::class
    ]);

    Route::post('user/changepassword', [UserController::class, 'changePassword'])->name('changePassword');

    Route::resource('freelancer' , FreelancerController::class);


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

    // for Report
    Route::get('reports', [ReportController::class, 'index']);
    Route::get('newReports', [ReportController::class, 'newReports']);
    Route::post('report/send', [ReportController::class, 'store']);
    Route::post('report/reply', [ReportController::class, 'reply']);

    // for PayPal
    Route::post('paypal'       ,  [PayPalController::class, 'paypal' ])->name('paypal');
    Route::get('paypal/success',  [PayPalController::class, 'success'])->name('paypal.success');
    Route::get('paypal/cancel' ,  [PayPalController::class, 'cancel' ])->name('paypal.cancel');

    // for Document Ai
    Route::get('documentAi', [DocumentAIController::class, 'processDocument']);

    // for CVs
    Route::post('/generate-cv', [CVController::class, 'create']);

    Route::middleware(['auth:api', 'can:isCompany']) ->group(function(){

    });

    Route::middleware(['auth:api', 'can:isFreelancer']) ->group(function(){
    });
});
