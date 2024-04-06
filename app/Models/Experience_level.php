<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Experience_level extends Model
{
    use HasFactory;

    public function job_details(): HasMany
    {
        return $this->hasMany(JobDetail::class);
    }
}
