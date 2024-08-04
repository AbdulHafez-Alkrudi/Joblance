<?php

namespace App\Models\Users\Favourite;

use App\Models\Users\Company\JobDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FavouriteJob extends Model
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

    public function get_all_favourite_jobs($favourite_jobs)
    {
        foreach ($favourite_jobs as $key => $favourite_job)
        {
            $favourite_jobs[$key] = $this->get_favourite_job($favourite_job);
        }
        return $favourite_jobs;
    }

    public function get_favourite_job($favourite_job)
    {
        return [
            'id' => $favourite_job->id,
            'job_detail' => (new JobDetail)->get_job_detail($favourite_job->job_detail, request('lang'))
        ];
    }
}
