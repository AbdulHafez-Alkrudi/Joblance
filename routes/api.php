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
    Users\Favourite\FavouriteFreelancerController,
    Users\Favourite\FavouriteJobController,
    Users\Favourite\FavouriteTaskController,
    Notification\NotificationController,
    Users\Freelancer\OfferController,
    Payment\PayPalController,
    Report\ReportController,
    Users\Company\CompanyController,
    Review\ReviewController,
    TaskStateController,
    Users\SubscriptionController,
    Users\Freelancer\FreelancerController,
    Users\Freelancer\SkillController,
    Users\MajorController,
    Users\TaskController,
    Users\UserController,
    Users\UserProjects\UserProjectController,
    Users\UserProjects\UserSkillsController};
use App\Http\Controllers\MonthlyReport\MonthlyReportController;
use App\Http\Controllers\Payment\BudgetController;
use App\Http\Controllers\Payment\PriceController;
use App\Http\Controllers\Payment\TransactionController;
use App\Http\Controllers\Review\EvaluationController;
use App\Http\Controllers\Users\AcceptedTasksController;
use App\Http\Controllers\Users\Company\AcceptedJobsController;
use App\Http\Controllers\Users\Company\ImportantJobsController;
use App\Http\Controllers\Users\Company\JobDetailController;
use App\Http\Controllers\Users\Company\JobTypeController;
use App\Http\Controllers\Users\Company\RemoteController;
use App\Http\Controllers\Users\FollowerController;
use App\Http\Controllers\Users\Freelancer\ExperienceLevelController;
use App\Http\Controllers\Users\Freelancer\JobApplicationController;
use App\Http\Controllers\Users\Freelancer\TagController;
use App\Http\Controllers\Users\UserProjects\UserTagsController;
use App\Http\Controllers\Users\Freelancer\StudyCaseController;

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

    // Offer
    Route::prefix('offer')->group(function () {
        Route::get('', [OfferController::class, 'index']);
        Route::get('{id}', [OfferController::class, 'show']);
    });

    // Job Applications
    Route::prefix('jobApplication')->group(function () {
        Route::get('', [JobApplicationController::class, 'index']);
        Route::get('{id}', [JobApplicationController::class, 'show']);
    });

    // Job Details
    /*Route::prefix('jobDetail')->group(function () {
        Route::get('', [JobDetailController::class, 'index']);
        Route::get('{id}', [JobDetailController::class, 'show']);
    });*/

    // Important Jobs
    Route::prefix('important_job')->group(function () {
        Route::get('', [ImportantJobsController::class, 'index']);
        Route::get('{id}', [ImportantJobsController::class, 'show']);
    });

    Route::prefix('accepted_jobs')->group(function () {
        Route::get('', [AcceptedJobsController::class, 'index']);
        Route::get('{id}', [AcceptedJobsController::class, 'show']);
    });

    // Resource routes
    Route::apiResources([
        'user_skills'     => UserSkillsController::class,
        'userProject'     => UserProjectController::class,
        'review'          => ReviewController::class,
        'evaluation'      => EvaluationController::class,
        'user'            => UserController::class,
        'freelancer'      => FreelancerController::class,
        'company'         => CompanyController::class,
        'task'            => TaskController::class,
        'follower'        => FollowerController::class,
        'tag'             => TagController::class,
        'user_tags'       => UserTagsController::class,
        'accepted_tasks'  => AcceptedTasksController::class,
        'favourite_job'   => FavouriteJobController::class,
        'experienceLevel' => ExperienceLevelController::class,
        'favourite_task'  => FavouriteTaskController::class,
        'major'           => MajorController::class,
        'favourite_freelancer' => FavouriteFreelancerController::class,
        'job_type'        => JobTypeController::class,
        'study_case'      =>StudyCaseController::class,
    ]);
    // Search Skills
    Route::get('skills/search', [SkillController::class, 'search']);

    // Subscription
    Route::prefix('subscription')->group(function () {
        Route::get('', [SubscriptionController::class, 'index']);
        Route::post('', [SubscriptionController::class, 'store']);
        Route::delete('', [SubscriptionController::class, 'destroy']);
    });

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

    // Document AI route
    Route::get('documentAi', [DocumentAIController::class, 'processDocument']);

    // Middleware-specific routes (empty groups for future expansion)
    Route::middleware(['auth:api', 'can:isCompany', 'subscribed'])->group(function () {
        // Job Detail
        Route::prefix('jobDetail')->group(function () {
            Route::post('', [JobDetailController::class, 'store']);
            Route::post('{id}', [JobDetailController::class, 'update']);
            Route::delete('{id}', [JobDetailController::class, 'destroy']);
        });

        // Important Job
        Route::post('important_job', [ImportantJobsController::class, 'store']);

        // Accepted Job
        Route::post('accepted_jobs', [AcceptedJobsController::class, 'store']);
    });

    Route::middleware(['auth:api', 'can:isAdmin'])->group(function () {
        Route::apiResources([
            'skill'           => SkillController::class,
            'task_state'      => TaskStateController::class,
            'remote'          => RemoteController::class
        ]);

        // Transactions route
        Route::get('users/{userID}/transactions', [TransactionController::class, 'index']);

        // Budget routes
        Route::prefix('budget')->group(function () {
            Route::post('charge', [BudgetController::class, 'charge']);
            Route::get('search', [BudgetController::class, 'search']);
        });

        // Report route
        Route::prefix('reports')->group(function () {
            Route::get('/', [ReportController::class, 'index']);
            Route::get('newReports', [ReportController::class, 'newReports']);
            Route::post('reply', [ReportController::class, 'reply']);
        });

        // Add Tag To Skills
        Route::post('tag/addToSkills/{id}', [TagController::class, 'addToSkills']);

        // Price
        Route::resource('price', PriceController::class);
        Route::post('price/{price}' , [PriceController::class , 'update']);
        // Monthly Report
        Route::get('monthly_report', MonthlyReportController::class);
    });

    Route::middleware(['auth:api', 'can:isFreelancer'])->group(function () {
        // Budget routes
        Route::get('budget/{id}', [BudgetController::class, 'get_budget']);
        Route::post('freelancer/{freelancer}', [FreelancerController::class, 'update']);
        // CV route
        Route::post('generate-cv', [CVController::class, 'create']);

        // Apply for job
        Route::post('jobApplication', [JobApplicationController::class, 'store']);

        // Offer
        Route::prefix('offer')->group(function () {
            Route::post('', [OfferController::class, 'store']);
            Route::delete('{id}', [OfferController::class, 'destroy']);
        });
    });
});
