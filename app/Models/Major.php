<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Major extends Model
{
    use HasFactory;
    protected $fillable = ['name_EN' , 'name_AR'] ;
    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }
}
