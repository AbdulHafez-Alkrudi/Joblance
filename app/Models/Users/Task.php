<?php

namespace App\Models\Users;

use App\Http\Controllers\Users\UserController;
use App\Models\Users\Favoutite\FavouriteTask;
use App\Models\User;
use App\Models\Users\Company\Company;
use App\Models\Users\Freelancer\Offer;
use Carbon\Carbon;
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
        'title' ,
        'about_task' ,
        'requirements' ,
        'additional_information' ,
        'duration' ,
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

    public function favoutite_tasks() : HasMany
    {
        return $this->hasMany(FavouriteTask::class, 'task_id', 'id');
    }

    public function get_all_tasks($tasks, $lang)
    {
        // in this function, I'll get all the tasks as a parameter and add to them the image and
        // the name of the user

        foreach ($tasks as $key => $task) {
            $tasks[$key] = $this->get_task($task, $lang);
        }
        return $tasks;
    }

    public function get_task($task, $lang)
    {
        $user = $task->user->userable;
        return [
            'id' => $task->id,
            'user_id' => $task->user_id,
            'name' => $user['name'] ? $user['name'] : $user['first_name'].' '.$user['last_name'],
            'image' => $user->image != null ? asset('storage/' . $user->image) : "",
            'role_id' => $task->user->role_id,
            'type' => (new UserController())->get_type($task->user),
            'task_title' => $task->title,
            'duration' => $task->duration,
            'active' => $task->active,
            'major_name' => (new Major)->get_major($task->major_id, $lang, 0),
            'description' => $task->about_task,
            'date' => $task->created_at->format('Y-m-d H:i:s'),
            'favourite' => auth()->user()->hasFavouriteTask($task->id)
        ];
    }
}
