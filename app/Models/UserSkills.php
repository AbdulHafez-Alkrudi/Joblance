<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserSkills extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'skill_id'
    ];
    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class) ;
    }
}
