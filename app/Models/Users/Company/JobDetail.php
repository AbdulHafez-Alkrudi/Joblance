<?php

namespace App\Models\Users\Company;

use App\Models\Users\Favoutite\FavouriteJob;
use App\Models\User;
use App\Models\Users\Freelancer\ExperienceLevel;
use App\Models\Users\Freelancer\JobApplication;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class JobDetail extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
        'updated_at' => 'datetime:Y-m-d',
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
    public function job_applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function job_type(): BelongsTo
    {
        return $this->belongsTo(JobType::class);
    }
    public function remote(): BelongsTo
    {
        return $this->belongsTo(Remote::class);
    }
    public function experience_level(): BelongsTo
    {
        return $this->belongsTo(ExperienceLevel::class);
    }

    public function important_job() : HasOne
    {
        return $this->hasOne(ImportantJobs::class);
    }

    public function favourite_jobs() : HasMany
    {
        return $this->hasMany(FavouriteJob::class, 'job_detail_id', 'id');
    }

    public function get_all_jobs_detail($jobs_detail, $lang)
    {
        foreach ($jobs_detail as $key => $job_detail)
        {
            $jobs_detail[$key] = $this->get_job_detail($job_detail, $lang);
        }
        return $jobs_detail;
    }

    public function get_job_detail($job_detail, $lang)
    {
        $company = $job_detail->company;
        return [
            'id' => $job_detail->id,
            'image' => $company->image != null ? asset('storage/' . $company->image) : "",
            'name'  => $company->name,
            'company_id' => $company->user->id,
            'job_title' => $job_detail->title,
            'date' => $job_detail->created_at->format('Y-m-d H:i:s'),
            'remote_name' => (new Remote)->get_remote($job_detail->remote_id, $lang, 0),
            'location' => $job_detail->location,
            'active' => $job_detail->active,
            'number_of_applicants' => count($job_detail->job_applications)
        ];
    }
}
