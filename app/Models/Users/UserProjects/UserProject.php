<?php

namespace App\Models\Users\UserProjects;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserProject extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'project_name',
        'project_description',
        'link'
    ];
    public function images(): HasMany
    {
        return $this->hasMany(UserProjectImage::class , 'project_id');
    }

    public function store($data)
    {
        return $this->create($data);
    }
}
