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
        'task_title' ,
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
    public function scopeFilter($query , array $filters)
    {

        // searching according to a specific title:
        $query->when($filters['task_title'] ?? false , fn($query , $task_title) =>
                 $query->where('task_title' , 'REGEXP' , $task_title)
        );

        // searching according to a specific user:
        $query->when($filters['user_id'] ?? false , fn($query , $user_id) =>
                 $query->where('user_id' , $user_id)
        );

        // searching according to a specific major:
        $query->when($filters['major_id'] ?? false , fn($query , $major_id) =>
                     $query->where('major_id' , $major_id)
        );


        // searching about a task with a maximum duration:

        $query->when($filters['duration'] ?? false , fn($query , $duration) =>
                $query->where('durat ion' , '<=' , $duration)
        );


        // searching according to posted date of the job:

        $last_week_date = Carbon::now()->subWeek();
        $last_month_date= Carbon::now()->subMonth();
        $query->when($filters['date_posted'] ?? false , fn($query , $date_posted)=>
            $query->when($date_posted == 'last week' ,
                // return the jobs that were posted last week
                fn($query) =>
                $query->where('created_at' , '>=' , $last_week_date),
                // else return the jobs that were posted last month
                fn($query)=>
                $query->where('created_at' , '>=' , $last_month_date)
            )
        );
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
            'budget_min'=>$task->budget_min,
            'budget_max'=>$task->budget_max,
            'requirements'=>$task->requirements,
            'date' => $task->created_at->format('Y-m-d H:i:s'),
            'major_id' => $task->major_id,
            'favourite' => auth()->user()->hasFavouriteTask($task->id)
        ];
    }
}
