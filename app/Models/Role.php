<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    const ROLE_ADMINISTRATOR = 1;
    const ROLE_COMPANY = 2;
    const ROLE_FREELANCER = 3;

    protected $fillable = ['name'];
}
