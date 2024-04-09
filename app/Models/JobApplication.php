<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    use HasFactory;

    public function freelancer(): BelongsTo
    {
        return $this->belongsTo(Freelancer::class);
    }
    public function job_detail(): BelongsTo
    {
        return $this->belongsTo(JobDetail::class);
    }

}
