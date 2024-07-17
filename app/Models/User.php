<?php

namespace App\Models;

use App\Mail\SendCodeEmailVerification;
use App\Models\Auth\DeviceToken;
use App\Models\Auth\EmailVerification;
use App\Models\Chat\Conversation;
use App\Models\Chat\Message;
use App\Models\Payment\Budget;
use App\Models\Report\Report;
use App\Models\Review\Review;
use App\Models\Users\Company\Company;
use App\Models\Users\Evaluation;
use App\Models\Users\Favoutite\FavouriteFreelancer;
use App\Models\Users\Favoutite\FavouriteJob;
use App\Models\Users\Favoutite\FavouriteTask;
use App\Models\Users\Follower;
use App\Models\Users\Freelancer\Offer;
use App\Models\Users\Freelancer\Tag;
use App\Models\Users\Role;
use App\Models\Users\Subscription;
use App\Models\Users\Task;
use App\Models\Users\UserProjects\UserSkills;
use App\Models\Users\UserProjects\UserTags;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;


    const COMPANY    = 1 ;
    const FREELANCER = 2 ;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'phone_number',
        'email',
        'email_verified',
        'password',
        'role_id',
        'userable_id',
        'userable_type',
        'device_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'created_at' => 'datetime:Y-m-d',
        'updated_at' => 'datetime:Y-m-d',
    ];

    public function userable(): MorphTo
    {
        return $this->morphTo();
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function budget()
    {
        return $this->hasOne(Budget::class);
    }

    public function followers(): HasMany
    {
        return $this->hasMany(Follower::class);
    }

    public function followings()
    {
        return $this->hasMany(Follower::class, 'follower_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    public function deviceToken(): HasMany
    {
        return $this->hasMany(DeviceToken::class);
    }

    public function routeNotificationForFcm($notification = null)
    {
        return $this->deviceToken()->pluck('token')->toArray();
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    public function hasActiveSubscription()
    {
        return $this->subscription && $this->subscription->ends_at->isFuture();
    }

    public function hasApplication($job_detail_id)
    {
        return $this->userable->job_applications()->where('job_detail_id', $job_detail_id)->exists();
    }

    public function hasOffer($task_id)
    {
        return $this->offers()->where('task_id', $task_id)->first();
    }

    public function hasEvaluated($freelancer_id)
    {
        return $this->evaluations()->where('freelancer_id', $freelancer_id)->exists();
    }

    public function hasFollow($user)
    {
        return $user->followers()->where('follower_id', Auth::id())->exists();
    }

    public function hasSkill($id)
    {
        $userSkill = $this->skills()->where('skill_id', $id)->first();
        return !is_null($userSkill);
    }

    public function hasTag($name)
    {
        $tag = Tag::query()->where('name', $name)->first();
        if (is_null($tag)) {
            return false;
        }

        $userTag = $this->tags()->where('tag_id', $tag->id)->first();
        return !is_null($userTag);
    }

    public function hasReview($company_id)
    {
        return $this->reviews()->where('company_id', $company_id)->exists();
    }

    public function reports() :HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(UserSkills::class);
    }

    public function tags() : HasMany
    {
        return $this->hasMany(UserTags::class);
    }

    public function offers() : HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function favourite_jobs() : HasMany
    {
        return $this->hasMany(FavouriteJob::class, 'user_id', 'id');
    }

    public function favourite_tasks() : HasMany
    {
        return $this->hasMany(FavouriteTask::class, 'user_id', 'id');
    }

    public function favourite_freelancers() : HasMany
    {
        return $this->hasMany(FavouriteFreelancer::class, 'user_id', 'id');
    }

    public function hasFavouriteTask($task_id)
    {
        return $this->favourite_tasks()->where('task_id', $task_id)->exists();
    }

    public function hasfavouriteJob($job_detail_id)
    {
        return $this->favourite_jobs()->where('job_detail_id', $job_detail_id)->exists();
    }

    public function hasFavouriteFreelancer($freelancer_id)
    {
        return $this->favourite_freelancers()->where('freelancer_id', $freelancer_id)->exists();
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'participants')
            ->latest('last_message_id')
            ->withPivot([
                'role', 'joined_at'
            ]);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'user_id', 'id');
    }

    public function receivedMessages()
    {
        return $this->belongsToMany(Message::class, 'recipients')
            ->withPivot([
                'read_at', 'deleted_at',
            ]);
    }

    public function showParticipant($participant)
    {
        $participant_data = [
            'id'    => $participant->id,
            'image' => $participant->userable->image,
            'role'  => $participant->pivot->role,
        ];

        if ($participant->userable_type == Company::class)
            $participant_data['name'] = $participant->userable->name;
        else
            $participant_data['name'] = $participant->userable->first_name.' '.$participant->userable->last_name;

        return $participant_data;
    }

    public function sendCode($email)
    {
        // Delete all old code that user send before
        EmailVerification::query()->where('email', $email)->delete();

        $data['email'] = $email;

        // Generate random code
        $data['code'] = mt_rand(100000, 999999);

        // Create a new code
        $codeData = EmailVerification::query()->create($data);

        // Send email to user
        Mail::to($email)->send(new SendCodeEmailVerification($codeData['code']));
    }
}
