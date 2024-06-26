<?php

namespace App\Models\Users\Company;

use App\Models\User;
use App\Models\Users\Freelancer\ExperienceLevel;
use App\Models\Users\Freelancer\JobApplication;
use App\Models\Users\Major;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobDetail extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }
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

    public function get_all_jobs($jobs)
    {
        foreach($jobs as $key => $job)
        {
            $jobs[$key] = $this->get_job($job);
        }
        return $jobs;
    }
    public function get_job($job)
    {
        $company = User::find($job['company_id'])->userable();
        $job['company_name'] = $company->name;
        $job['image'] =  $company->image != null ? asset('storage/' . $company->image) : "";
        return $job ;
    }

    public function scopeFilter($query , array $filters)
    {

        // searching according to a specific job type:
        $query->when($filters['job_type_id'] ?? false , fn($query , $job_type_id) =>
                $query->where('job_type_id' , $job_type_id)
        );

        // searching according to a specific experience level:
        $query->when($filters['experience_level_id'] ?? false , fn($query , $experience_level_id) =>
                $query->where('experience_level_id' , $experience_level_id)
        );

        // searching according to the remote type:
        $query->when($filters['remote_type'] ?? false , fn($query , $remote_type) =>
                $query->where('remote_type' , $remote_type)
        );

        // searching according to a specific major:
        $query->when($filters['major_id'] ?? false , fn($query , $major_id) =>
                 $query->where('major_id' , $major_id)
        );

        // searching according to posted date of the job:

        $last_week_date = Carbon::now()->subWeek();
        $last_month_date= Carbon::now()->subMonth();
        $query->when($filters['date_posted'] ?? false , fn($query , $date_posted)=>
                $query->when($date_posted == 'last week' ,
                    // return the jobs that were posted last week
                    fn($query) =>
                        $query->where('created_at' , '>=' , $last_week_date),
                    // else return the jobs that were posted last month
                    fn($query)=>
                        $query->where('created_at' , '>=' , $last_month_date)
                )
        );


    }
}
