<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{Auth\EmailVerificationController,
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
    Users\Freelancer\OfferController,
    Payment\PayPalController,
    Report\ReportController,
    Users\Company\CompanyController,
    Review\ReviewController,
    Users\SubscriptionController,
    Users\Freelancer\FreelancerController,
    Users\Freelancer\SkillController,
    Users\MajorController,
    Users\TaskController,
    Users\UserController,
    Users\UserProjects\UserProjectController,
    Users\UserProjects\UserSkillsController};
use App\Http\Controllers\Payment\BudgetController;
use App\Http\Controllers\Payment\TransactionController;
use App\Http\Controllers\Review\EvaluationController;
use App\Http\Controllers\Users\Company\JobDetailController;
use App\Http\Controllers\Users\FollowerController;
use App\Http\Controllers\Users\Freelancer\ExperienceLevelController;
use App\Http\Controllers\Users\Freelancer\JobApplicationController;

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
        'user_skills'     => UserSkillsController::class,
        'userProject'     => UserProjectController::class,
        'review'          => ReviewController::class,
        'evaluation'      => EvaluationController::class,
        'subscription'    => SubscriptionController::class,
        'user'            => UserController::class,
        'major'           => MajorController::class,
        'skill'           => SkillController::class,
        'freelancer'      => FreelancerController::class,
        'company'         => CompanyController::class,
        'task'            => TaskController::class,
        'offer'           => OfferController::class,
        'jobApplication'  => JobApplicationController::class,
        'experienceLevel' => ExperienceLevelController::class,
        'jobDetail'       => JobDetailController::class,
        'follower'        => FollowerController::class
    ]);

    // Search Skills
    Route::get('skills/search', [SkillController::class, 'search']);
    // Custom update routes
    Route::post('userProject/{userProject}', [UserProjectController::class, 'update']);
    Route::post('company/{company}', [CompanyController::class, 'update']);
    Route::post('freelancer/{freelancer}', [FreelancerController::class, 'update']);
    Route::post('task/{task}' , [TaskController::class , 'update']);
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

    // Report route
    Route::post('reports/send', [ReportController::class, 'store']);

    // PayPal routes
    Route::prefix('paypal')->group(function () {
        Route::post('/', [PayPalController::class, 'paypal'])->name('paypal');
        Route::get('success', [PayPalController::class, 'success'])->name('paypal.success');
        Route::get('cancel', [PayPalController::class, 'cancel'])->name('paypal.cancel');
    });

    // Budget routes
    Route::prefix('budget')->group(function () {
        Route::get('details', [BudgetController::class, 'get_budget']);
        Route::post('pay', [BudgetController::class, 'pay']);
    });

    // Document AI route
    Route::get('documentAi', [DocumentAIController::class, 'processDocument']);

    // Send Report
    Route::post('reports/send', [ReportController::class, 'store']);

    // Middleware-specific routes (empty groups for future expansion)
    Route::middleware(['auth:api', 'can:isCompany', 'subscribed'])->group(function () {
        // Add company-specific routes here
    });

    Route::middleware(['auth:api', 'can:isFreelancer'])->group(function () {
        // CV route
        Route::post('generate-cv', [CVController::class, 'create']);
    });

    Route::middleware(['auth:api', 'can:isAdmin'])->group(function () {
        // Transactions route
        Route::get('users/{userID}/transactions', [TransactionController::class, 'index']);

        // Budget route
        Route::post('budget/charge', [BudgetController::class, 'charge']);

        // Report route
        Route::prefix('reports')->group(function () {
            Route::get('/', [ReportController::class, 'index']);
            Route::get('newReports', [ReportController::class, 'newReports']);
            Route::post('reply', [ReportController::class, 'reply']);
        });
    });
});
