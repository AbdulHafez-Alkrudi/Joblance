<?php

namespace App\Models\Users\Freelancer;

use App\Models\User;
use App\Models\Users\Company\JobDetail;
use App\Models\Users\Major;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function freelancer(): BelongsTo
    {
        return $this->belongsTo(Freelancer::class);
    }
    public function job_detail(): BelongsTo
    {
        return $this->belongsTo(JobDetail::class);
    }

    public function get_all_job_applications($job_detail_id, $lang)
    {
        $job_applications = $this->where('job_detail_id', $job_detail_id)->get();
        foreach ($job_applications as $key => $job_application)
        {
            $job_applications[$key] = $this->get_job_application($job_application, $lang);
        }
        return $job_applications;
    }

    public function get_job_application($job_application, $lang)
    {
        $user = User::find($job_application->freelancer_id)->userable;
        $job_application['major_name'] = (new Major)->get_major($user->major_id, $lang, 0);
        $job_application['image'] = $user->image != null ? asset('storage/' . $user->image) : "";
        $job_application['name']  = $user['first_name'].' '.$user['last_name'];

        return $job_application;
    }
}
