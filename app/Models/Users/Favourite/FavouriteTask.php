<?php

namespace App\Models\Users\Favourite;

use App\Models\Users\Task;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FavouriteTask extends Model
{
    use HasFactory;

    protected $fillable = ['task_id', 'user_id'];

    public function task() : BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function get_all_favourite_tasks($favourite_tasks)
    {
        foreach ($favourite_tasks as $key => $favourite_task)
        {
            $favourite_tasks[$key] = $this->get_favourite_task($favourite_task);
        }
        return $favourite_tasks;
    }

    public function get_favourite_task($favourite_task)
    {
        return [
            'id' => $favourite_task->id,
            'task' => (new Task)->get_task($favourite_task->task, request('lang'))
        ];
    }
}
