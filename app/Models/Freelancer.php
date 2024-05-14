<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use JetBrains\PhpStorm\ArrayShape;
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
    protected $fillable = [
        'first_name',
        'last_name',
        'major_id',
        'location',
        'study_case',
        'open_to_work',
        'user_id',
        'image',
        'study_case_id',
        'birth_date',
        'bio',
        'gender',
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
   // This method returns the freelancer information according to requested language
    public function get_info(Freelancer $freelancer , string $lang): array
    {
        return [
            'id'            => $freelancer->user->id,
            'first_name'    => $freelancer->first_name ,
            'last_name'     => $freelancer->last_name,
            'image'         => $freelancer->image,
            'bio'           => is_null($freelancer->bio) ? "" : $freelancer->bio,
            'major'         => (new Major)->get_major($freelancer->major_id , $lang , false),
            'major_id'      => $freelancer->major_id,
            'study_case'    => (new StudyCase)->get_study_case($freelancer->study_case_id, $lang, false),
            'study_case_id' => $freelancer->study_case_id,
            'location'      => $freelancer->location,
            'open_to_work'  => $freelancer->open_to_work,
        ];
    }


    public function get_all_freelancers(string $lang): Collection
    {
        $freelancers = $this->all();
        foreach($freelancers as $key => $freelancer){
            $freelancers[$key] = $this->get_info($freelancer , $lang);
        }
        return $freelancers;
    }
}
