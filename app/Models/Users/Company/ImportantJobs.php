<?php

namespace App\Models\Users\Company;

use App\Models\Payment\Price;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportantJobs extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_detail_id',
        'expiry_date',
        'price_id'
    ];

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

    public function job_detail() : BelongsTo
    {
        return $this->belongsTo(JobDetail::class);
    }

    public function price() : BelongsTo
    {
        return $this->belongsTo(Price::class);
    }

    public function get_all_important_jobs($importantJobs, $lang)
    {
        foreach ($importantJobs as $key => $importantJob)
        {
            $importantJobs[$key] = $this->get_important_job($importantJob, $lang);
        }
        return $importantJobs;
    }

    public function get_important_job($importantJob, $lang)
    {
        return [
            'id' => $importantJob->id,
            'expiry_date' => (Carbon::parse($importantJob->created_at)->copy()->addDays(10))->diffInDays(Carbon::now()),
            'price' => (new Price)->get_price($importantJob->price_id, $lang, 1)->price,
            'date' => $importantJob->created_at->format('Y-m-d'),
            'job_detail' => (new JobDetail)->get_job_detail($importantJob->job_detail, $lang),
        ];
    }
}
