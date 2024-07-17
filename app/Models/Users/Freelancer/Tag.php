<?php

namespace App\Models\Users\Freelancer;

use App\Models\Users\UserProjects\UserTags;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'count'];

    public function userTags() : HasMany
    {
        return $this->hasMany(UserTags::class);
    }

    public function get_tag(UserTags $user_tag)
    {
        return [
            'id'       => $user_tag->id,
            'tag_id'   => $user_tag->tag_id,
            'tag_name' => $user_tag->tag->name
        ];
    }
}
