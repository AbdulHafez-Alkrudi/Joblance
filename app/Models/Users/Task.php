<?php

namespace App\Models\Users;

use App\Models\User;
use App\Models\Users\Company\Company;
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
    public function get_all_tasks($tasks)
    {
        foreach ($tasks as $key => $task) {
            $tasks[$key] = $this->get_task($task);
        }
        return $tasks;
    }

    public function get_task($task)
    {
        $user = User::find($task->user_id)->userable;
        $task['image'] = $user->image;
        $task['name']  = $user['name'] ? $user['name'] : $user['first_name'].' '.$user['last_name'];

        return $task;
    }
}
