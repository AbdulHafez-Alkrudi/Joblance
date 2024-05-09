<?php

namespace App\Models;

use App\Http\Controllers\Users\UserController;
use App\Mail\SendCodeEmailVerification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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

    public function deviceToken() : HasMany
    {
        return $this->hasMany(DeviceToken::class);
    }

    public function routeNotificationForFcm($notification = null)
    {
        return $this->deviceToken()->pluck('token')->toArray();
    }

    public function reports() :HasMany
    {
        return $this->hasMany(Report::class);
    }
    public function skills(): HasMany
    {
        return $this->hasMany(UserSkills::class);
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
