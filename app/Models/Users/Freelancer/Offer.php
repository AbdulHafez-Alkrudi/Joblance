<?php

namespace App\Models\Users\Freelancer;

use App\Models\User;
use App\Models\Users\Task;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Offer extends Model
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
        'updated_at' => 'datetime:Y-m-d'
    ];

    public function task() : BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
