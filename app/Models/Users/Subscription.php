<?php

namespace App\Models\Users;

use App\Models\Payment\Price;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'starts_at', 'ends_at', 'price_id'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'starts_at'  => 'datetime:Y-m-d',
        'ends_at'    => 'datetime:Y-m-d',
        'created_at' => 'datetime:Y-m-d',
        'updated_at' => 'datetime:Y-m-d',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'starts_at',
        'ends_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function price() : BelongsTo
    {
        return $this->belongsTo(Price::class);
    }

    public function get_info(Subscription $subscription)
    {
        $price = (new Price)->get_price($subscription->price_id, request('lang'), 1);
        return [
            'type_of_subscription' => $price->name,
            'starts_at' => $subscription->starts_at->format('Y-m-d'),
            'ends_at' => $subscription->ends_at->format('Y-m-d')
        ];
    }
}
