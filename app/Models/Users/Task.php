<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'task_title' ,
        'about_task' ,
        'requirements' ,
        'additional_information' ,
        'task_duration' ,
        'budget_min' ,
        'budget_max'
    ];

    // TODO: write the filter scope


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function get_all_tasks($user_id): Collection
    {
        return collect(Task::query()->where('user_id' , $user_id)->get());
    }
}
