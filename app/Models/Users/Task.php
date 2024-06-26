<?php

namespace App\Models\Users;

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
        // in this function, I'll get all the tasks as a parameter and add to them the image and
        // the name of the user

        foreach ($tasks as $key => $task) {
            $tasks[$key] = $this->get_task($task, $lang);
        }
        return $tasks;
    }

    public function get_task($task, $lang)
    {
        $user = User::find($task->user_id);
        $task['major_name'] = (new Major)->get_major($task->major_id, $lang, 0);
        $task['image'] = $user->userable->image != null ? asset('storage/' . $user->userable->image) : "";
        $task['name']  = $user->userable['name'] ? $user->userable['name'] : $user->userable['first_name'].' '.$user->userable['last_name'];
        $task['user_role'] = $user["role_id"];
        return $task;
    }

    public function scopeFilter($query , array $filter)
    {
        // searching according to a specific major:
        $query->when($filters['major_id'] ?? false , fn($query , $major_id) =>
                 $query->where('major_id' , $major_id)
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
}
