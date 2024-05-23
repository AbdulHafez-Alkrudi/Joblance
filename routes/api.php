<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    Auth\EmailVerificationController,
    Auth\GoogleLoginController,
    Auth\LoginController,
    Auth\LogoutController,
    Auth\RegisterController,
    Auth\ResetCodePasswordController,
    Chat\ConversationController,
    Chat\MessageController,
    CV\CVController,
    DocumentAI\DocumentAIController,
    Notification\NotificationController,
    Payment\PayPalController,
    Report\ReportController,
    Users\Company\CompanyController,
    Review\ReviewController,
    Users\Freelancer\FreelancerController,
    Users\Freelancer\SkillController,
    Users\MajorController,
    Users\UserController,
    Users\UserProjects\UserProjectController,
    Users\UserProjects\UserSkillsController};
use App\Http\Controllers\Review\EvaluationController;



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

// Broadcast routes
Broadcast::routes(['middleware' => ['auth:api']]);

// Placeholder route
Route::get('contests', function ($id) {
    return view('welcome');
});

// Password reset routes
Route::prefix('user/password')->group(function () {
    Route::post('email', [ResetCodePasswordController::class, 'userForgotPassword']);
    Route::post('email/resend', [ResetCodePasswordController::class, 'userResendCode']);
    Route::post('code/check', [ResetCodePasswordController::class, 'userCheckCode']);
    Route::post('reset', [ResetCodePasswordController::class, 'userResetPassword']);
});

// Email verification routes
Route::prefix('user/email/code')->group(function () {
    Route::post('check', [EmailVerificationController::class, 'userCheckCode']);
    Route::post('resend', [EmailVerificationController::class, 'userResendCode']);
});

// Google login routes
Route::prefix('auth/google')->group(function () {
    Route::post('login', [GoogleLoginController::class, 'googleLogin']);
    Route::post('userinfo', [GoogleLoginController::class, 'getUserINfo']);
});

// Authentication routes
Route::post('register', RegisterController::class);
Route::post('login', [LoginController::class, 'login']);

Route::middleware(['auth:api'])->group(function () {
    // Logout route
    Route::post('user/logout', LogoutController::class)->name('logout');

    // Resource routes
    Route::apiResources([
        'user'        => UserController::class,
        'major'       => MajorController::class,
        'skill'       => SkillController::class,
        'user_skills' => UserSkillsController::class,
        'freelancer'  => FreelancerController::class,
        'userProject' => UserProjectController::class,
        'company'     => CompanyController::class,
        'review'      => ReviewController::class
    ]);

    // Custom update routes
    Route::post('userProject/{userProject}', [UserProjectController::class, 'update']);
    Route::post('company/{company}', [CompanyController::class, 'update']);
    Route::post('freelancer/{freelancer}', [FreelancerController::class, 'update']);

    // Change password route
    Route::post('user/changepassword', [UserController::class, 'changePassword'])->name('changePassword');

    // Notification routes
    Route::prefix('user')->group(function () {
        Route::post('mynotifications', [NotificationController::class, 'myNotifications'])->name('myNotifications');
        Route::post('newnotifications', [NotificationController::class, 'newNotifications'])->name('newNotifications');
    });

    // Conversation routes
    Route::prefix('conversations')->group(function () {
        Route::get('/', [ConversationController::class, 'index']);
        Route::get('{id}', [ConversationController::class, 'show']);
        Route::post('addparticipant', [ConversationController::class, 'addParticipant']);
        Route::delete('removeparticipant', [ConversationController::class, 'removeParticipant']);
        Route::put('{id}/read', [ConversationController::class, 'markAsRead']);
        Route::delete('delete/{id}', [ConversationController::class, 'destroy']);

        // Message routes
        Route::get('{id}/messages', [MessageController::class, 'getMessages']);
        Route::post('message/send', [MessageController::class, 'sendMessage']);
        Route::delete('message/{id}/delete', [MessageController::class, 'deleteMessage']);
    });

    // Report routes
    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'index']);
        Route::get('newReports', [ReportController::class, 'newReports']);
        Route::post('send', [ReportController::class, 'store']);
        Route::post('reply', [ReportController::class, 'reply']);
    });

    // PayPal routes
    Route::prefix('paypal')->group(function () {
        Route::post('/', [PayPalController::class, 'paypal'])->name('paypal');
        Route::get('success', [PayPalController::class, 'success'])->name('paypal.success');
        Route::get('cancel', [PayPalController::class, 'cancel'])->name('paypal.cancel');
    });

    // Document AI route
    Route::get('documentAi', [DocumentAIController::class, 'processDocument']);

    // CV route
    Route::post('generate-cv', [CVController::class, 'create']);

    // Middleware-specific routes (empty groups for future expansion)
    Route::middleware(['auth:api', 'can:isCompany'])->group(function () {
        // Add company-specific routes here
    });

    Route::middleware(['auth:api', 'can:isFreelancer'])->group(function () {
        // Add freelancer-specific routes here
    });
});
