<?php

namespace App\Models\Users;

use App\Http\Controllers\Users\UserController;
use App\Models\User;
use App\Models\Users\Task;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcceptedTasks extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'duration',
        'task_state_id'
    ];

    public function task() : BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function get_all_accepted_tasks($accepted_tasks)
    {
        foreach ($accepted_tasks as $key => $accepted_task)
        {
            $accepted_tasks[$key] = $this->get_info($accepted_task);
        }
        return $accepted_tasks;
    }

    public function get_info($accepted_task)
    {
        return [
            'id' => $accepted_task->id,
            'name' => $accepted_task->user->userable->first_name . ' ' . $accepted_task->user->userable->last_name,
            'image' => $accepted_task->user->userable->image,
            'duration' => $accepted_task->duration,
            'task_state_name' => (new TaskState)->get_task_state($accepted_task->task_state_id, request('lang'), 0)
        ];
    }
}
