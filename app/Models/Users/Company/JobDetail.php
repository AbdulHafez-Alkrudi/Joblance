<?php

namespace App\Models\Users\Company;

use App\Models\Users\Freelancer\ExperienceLevel;
use App\Models\Users\Freelancer\JobApplication;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobDetail extends Model
{
    use HasFactory;


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

}
