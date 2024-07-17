<?php

namespace App\Models\Users\UserProjects;

use App\Models\User;
use App\Models\Users\Freelancer\Tag;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTags extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'tag_id'];

    public function tag() : BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function get_tags($user_tags)
    {
        foreach ($user_tags as $key => $user_tag)
        {
            $user_tags[$key] = $user_tag->tag->get_tag($user_tag);
        }
        return $user_tags;
    }
}
