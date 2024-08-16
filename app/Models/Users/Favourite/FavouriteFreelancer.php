<?php

namespace App\Models\Users\Favourite;

use App\Models\User;
use App\Models\Users\Freelancer\Freelancer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FavouriteFreelancer extends Model
{
    use HasFactory;

    protected $fillable = ['freelancer_id', 'user_id'];

    public function freelancer() : BelongsTo
    {
        return $this->belongsTo(Freelancer::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function get_favourite_freelancers($favourite_freelancers)
    {
        foreach ($favourite_freelancers as $key => $favourite_freelancer)
        {
            $freelancer = User::find($favourite_freelancer->freelancer_id)->userable;
            $favourite_freelancers[$key] = (new Freelancer)->get_info($freelancer, request('lang'));;
        }
        return $favourite_freelancers;
    }
}
