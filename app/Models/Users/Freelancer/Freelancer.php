<?php

namespace App\Models\Users\Freelancer;

use App\Http\Resources\Freelancer\FreelancerResource;
use App\Models\Users\Favourite\FavouriteFreelancer;
use App\Models\User;
use App\Models\Users\Major;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;
use PhpParser\Node\Expr\Cast\Double;

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

    public function favourite_freelancers() : HasMany
    {
        return $this->hasMany(FavouriteFreelancer::class, 'freelancer_id', 'id');
    }
    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }
    public function scopeFilter($query , array $filters)
    {
        // searching according the name of the freelancer
        $query->when($filters['name'] ?? false , fn($query , $name) =>
                $query->where(DB::raw("CONCAT(first_name, ' ', last_name)") , "REGEXP" , $name)
        );


        // searching according to a specific study-case:
        $query->when($filters['study_case'] ?? false , fn($query , $study_case) =>
                $query->whereHas('study_case' , fn($query) =>
                        $query->where('name_EN' , 'REGEXP' , $study_case)
                            ->orWhere('name_AR' , 'REGEXP' , $study_case)
                )
        );
        // searching according to a specific major:
        $query->when($filters['major'] ?? false , fn($query , $major) =>
                $query->whereHas('major' , fn($query) =>
                        $query->where('name_EN' , 'REGEXP' , $major)
                            ->orWhere('name_AR' , 'REGEXP' , $major)
                )
        );

    }

   // This method returns the freelancer information according to requested language
    public function get_info(Freelancer $freelancer , string $lang): FreelancerResource
    {
        return new FreelancerResource($freelancer);
    }

    public function get_all_freelancers(string $lang , array $filters): LengthAwarePaginator
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
                )
            ->filter($filters)
            ->paginate();
    }

    public function rate($sum, $counter)
    {
        if($counter == 0) return 0 ;
        return $sum / ($counter * 1.0);
    }
}
