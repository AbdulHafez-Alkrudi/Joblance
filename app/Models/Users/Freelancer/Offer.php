<?php

namespace App\Models\Users\Freelancer;

use App\Models\User;
use App\Models\Users\Major;
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

    public function get_all_offers($task_id, $lang)
    {
        $offers = $this->where('task_id', $task_id)->get();
        foreach ($offers as $key => $offer) {
            $offers[$key] = $this->get_offer($offer, $lang);
        }
        return $offers;
    }

    public function get_offer($offer, $lang)
    {
        $user = User::find($offer->user_id)->userable;
        $offer['major_name'] = (new Major)->get_major($user->major_id, $lang, 0);
        $offer['image'] = $user->image != null ? asset('storage/' . $user->image) : "";
        $offer['name']  = $user['name'] ? $user['name'] : $user['first_name'].' '.$user['last_name'];

        return $offer;
    }
}
