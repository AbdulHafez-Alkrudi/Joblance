<?php

namespace App\Models\Users;

use App\Models\User;
use App\Models\Users\Company\Company;
use App\Models\Users\Freelancer\Offer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'major_id',
        'task_title' ,
        'about_task' ,
        'requirements' ,
        'additional_information' ,
        'task_duration' ,
        'budget_min' ,
        'budget_max',
        'active'
    ];

    // TODO: write the filter scope


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function major() : BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    public function offers() : HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function get_all_tasks($tasks, $lang)
    {
        foreach ($tasks as $key => $task) {
            $tasks[$key] = $this->get_task($task, $lang);
        }
        return $tasks;
    }

    public function get_task($task, $lang)
    {
        $user = User::find($task->user_id)->userable;
        $task['major_name'] = (new Major)->get_major($task->major_id, $lang, 0);
        $task['image'] = asset('storage/' . $user->image);
        $task['name']  = $user['name'] ? $user['name'] : $user['first_name'].' '.$user['last_name'];

        return $task;
    }
}
