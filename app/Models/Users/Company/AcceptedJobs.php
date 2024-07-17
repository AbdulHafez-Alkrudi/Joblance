<?php

namespace App\Models\Users\Company;

use App\Http\Controllers\Users\UserController;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcceptedJobs extends Model
{
    use HasFactory;

    protected $fillable = ['job_detail_id', 'user_id'];

    public function job_detail() : BelongsTo
    {
        return $this->belongsTo(JobDetail::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function get_all_accepted_jobs($accepted_jobs)
    {
        foreach ($accepted_jobs as $key => $accepted_job)
        {
            $accepted_jobs[$key] = $this->get_accepted_job($accepted_job);
        }
        return $accepted_jobs;
    }

    public function get_accepted_job($accepted_job)
    {
        $user = User::find($accepted_job->user_id);
        $accepted_job['user'] = $user->userable->get_info($user->userable, request('lang'), false);
        return $accepted_job;
    }
}
