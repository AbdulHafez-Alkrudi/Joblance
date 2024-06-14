<?php

namespace App\Models\Users\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Remote extends Model
{
    use HasFactory;

    public function job_details(): HasMany
    {
        return $this->hasMany(JobDetail::class);
    }
}
