<?php

namespace App\Models\Users\Freelancer;

use App\Http\Resources\Freelancer\FreelancerResource;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Freelancer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const MALE   = 1 ;
    const FEMALE = 2 ;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id',
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
        'email_verified_at' => 'date',
        'password' => 'hashed',
        'created_at' => 'datetime:Y-m-d',
        'updated_at' => 'datetime:Y-m-d'
    ];

    public function user(): MorphOne
    {
        return $this->MorphOne(User::class , 'userable');
    }

    public function study_case(): BelongsTo
    {
        return $this->belongsTo(StudyCase::class);
    }

    public function job_applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    // TODO: code the logic of the filters
    public function scopeFilter(Request $request , array $filters)
    {

    }

   // This method returns the freelancer information according to requested language
    public function get_info(Freelancer $freelancer , string $lang): FreelancerResource
    {
        return new FreelancerResource($freelancer);
    }

    public function get_all_freelancers(string $lang): LengthAwarePaginator
    {
        return Freelancer::query()
            ->select('id' ,
                    'first_name',
                    'last_name',
                    'image',
                    'bio',
                    'major_id',
                    'study_case_id',
                    'location',
                    'open_to_work',
                    'counter',
                    'sum_rate'
                )->paginate();
    }

    public function rate($sum, $counter)
    {
        if($counter == 0) return 0 ;
        return $sum / $counter;
    }
}
