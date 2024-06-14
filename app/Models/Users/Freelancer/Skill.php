<?php

namespace App\Models\Users\Freelancer;

use App\Models\Users\UserProjects\UserSkills;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Skill extends Model
{
    use HasFactory;
    protected $fillable = [
        'name'
    ];

    public function UserSkills(): HasMany
    {
        return $this->hasMany(UserSkills::class);
    }

    public function get_skill(UserSkills $user_skill)
    {
        $data = [
            'id'         => $user_skill->id,
            'skill_id'   => $user_skill->skill_id,
            'skill_name' => $user_skill->skill->name,
        ];

        return $data;
    }
}
