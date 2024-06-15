<?php

namespace App\Models\Review;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
        'updated_at' => 'datetime:Y-m-d',
    ];

    public function get_all_reviews($reviews)
    {
        foreach ($reviews as $key => $review) {
            $reviews[$key] = $this->get_info($review);
        }
        return $reviews;
    }

    public function get_info($review)
    {
        $user = User::find($review->user_id);
        return [
            'id' => $review->id,
            'level' => $review->level,
            'comment' => $review->comment,
            'user_id' => $review->user_id,
            'first_name' => $user->userable->first_name,
            'last_name' => $user->userable->last_name,
            'image' => $user->userable->image,
            'created_at' => $review->created_at->format('Y-m-d'),
        ];
    }
}
