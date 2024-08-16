<?php

namespace App\Models\Users\Company;

use App\Models\Users\Favoutite\FavouriteJob;
use App\Models\User;
use App\Models\Users\Freelancer\ExperienceLevel;
use App\Models\Users\Freelancer\JobApplication;
use App\Models\Users\Major;
use Carbon\Carbon;
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

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
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
        $company = User::find($job['company_id'])->userable;
        $job_type = JobType::find($job['job_type_id']);
        $experience_level = ExperienceLevel::find($job['experience_level_id']);
        $remote = Remote::find($job['remote_id']);
        $major   = Major::find($job['major_id']);
        $job['company_name'] = $company->name;

        $job['job_type_name']         = (request('lang') == 'ar' ? $job_type['name_AR'] : $job_type['name_EN']);
        $job['experience_level_name'] = (request('lang') == 'ar' ? $experience_level['name_AR'] : $experience_level['name_EN']);
        $job['remote_name']           = (request('lang') == 'ar' ? $remote['name_AR'] : $remote['name_EN']);
        $job['major_name']            = (request('lang') == 'ar' ? $major['name_AR'] : $major['name_EN']);

        $job['image'] =  $company->image != null ? asset('storage/' . $company->image) : "";
        return $job ;
    }

    public function scopeFilter($query , array $filters)
    {
        // searching according to a specific title:
        $query->when($filters['title'] ?? false , fn($query , $title) =>
                $query->where("title" , "REGEXP", $title)
        );
        $query->when($filters['location'] ?? false , fn($query , $location) =>
                $query->where("location" , "REGEXP", $location)
        );
        // searching according to a specific job type:
        $query->when($filters['job_type'] ?? false , fn($query , $job_type) =>
                $query->whereHas('job_type' , fn($query) =>
                        $query->where('name_EN' , "REGEXP" ,  $job_type)
                              ->orWhere('name_AR' , 'REGEXP' , $job_type)
                        )
        );




        // searching according to a specific experience level:
        $query->when($filters['experience_level'] ?? false , fn($query , $experience_level) =>
                $query->whereHas('experience_level' , fn($query) =>
                        $query->where('name_EN' , "REGEXP" , $experience_level)
                            ->orWhere('name_AR' , 'REGEXP' , $experience_level)
                )
        );

        // searching according to the remote type:
        $query->when($filters['remote_type'] ?? false , fn($query , $remote_type) =>
                $query->whereHas('remote' , fn($query) =>
                    $query->where('name_EN' , "REGEXP" , $remote_type)
                        ->orWhere('name_AR' , 'REGEXP' , $remote_type)
                    )
        );

        // searching according to a specific major:
        $query->when($filters['major'] ?? false , fn($query , $major) =>
                $query->whereHas('major' , fn($query) =>
                        $query->where('name_EN' , "REGEXP" ,  $major)
                            ->orWhere('name_AR' , 'REGEXP' , $major)
                        )
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
            'company_id' => $company->user->id,
            'company_name' => $company->name,
            'job_type_id' => $job_detail->job_type_id,
            'job_type_name' => (new JobType)->get_job_type($job_detail->job_type_id, $lang, 0),
            'experience_level_name' => (new ExperienceLevel)->get_experience_level($job_detail->experience_level_id, $lang, 0),
            'experience_level_id' => $job_detail->experience_level_id,
            'remote_name' => (new Remote)->get_remote($job_detail->remote_id, $lang, 0),
            'remote_id' => $job_detail->remote_id,
            'major_name' => (new Major)->get_major($job_detail->major_id, $lang, 0),
            'major_id' => $job_detail->major_id,
            'title' => $job_detail->title,
            'location' => $job_detail->location,
            'salary' => $job_detail->salary,
            'about_job' => $job_detail->about_job,
            'requirements' => $job_detail->requirements,
            'additional_information' => $job_detail->additional_information,
            'active' => $job_detail->active,
            'number_of_applicants' => count($job_detail->job_applications),
            'show_number_of_employees' => $job_detail->show_number_of_employees,
            'show_about_the_company' => $job_detail->show_about_the_company,
            'date' => $job_detail->created_at->format('Y-m-d H:i:s'),
            'favourite' => auth()->user()->hasfavouriteJob($job_detail->id)
        ];
    }
}
