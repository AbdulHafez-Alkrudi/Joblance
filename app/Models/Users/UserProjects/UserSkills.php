<?php

namespace App\Models\Users\UserProjects;

use App\Models\Users\Freelancer\Skill;
use App\Models\User;
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

    public function get_skills($user_skills)
    {
        foreach ($user_skills as $key => $user_skill)
        {
            $user_skills[$key] = $user_skill->skill->get_skill($user_skill);
        }

        return $user_skills;
    }
}
