<?php

namespace App\Models\Report;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'body', 'read_at'
    ];

    protected $casts = [
        'read_at'    => 'datetime:Y-m-d H:i:s',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $hidden = ['read_at'];

    public function user() :BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
