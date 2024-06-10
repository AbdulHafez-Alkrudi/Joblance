<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Company extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'major_id',
        'location',
        'num_of_employees',
        'user_id',
        'description',
        'image',
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
        return $this->morphOne(User::class , 'userable');
    }
    public function job_details(): HasMany
    {
        return $this->hasMany(JobDetail::class) ;
    }
    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    public function reviews() : HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function get_info(Company $company , string $lang)
    {
        return collect([
            'id'               => $company->user->id,
            'name'             => $company->name,
            'image'            => asset('storage/' . $company->image),
            'description'      => is_null($company->description) ? "" : $company->description,
            'major'            => (new Major)->get_major($company->major_id , $lang , false),
            'major_id'         => $company->major_id,
            'location'         => $company->location,
            'num_of_employees' => $company->num_of_employees,
            'subscriped'       => User::where('userable_id', $company->id)->first()->hasActiveSubscription(),
        ]);
    }

    public function get_all_companies(string $lang)
    {
        $companies = $this->all();
        return $companies->map(fn($company) => $this->get_info($company , $lang));
    }
}
