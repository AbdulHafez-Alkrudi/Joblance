<?php

namespace App\Models\Users\Freelancer;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExperienceLevel extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function job_details(): HasMany
    {
        return $this->hasMany(JobDetail::class);
    }

    public function get_all_experience_levels(string $lang) : Collection|array
    {
        return ExperienceLevel::query()->when($lang == 'en' ,
            function($query){
                return $query->select('id','name_EN as name');
            },
            function($query){
                return $query->select('id','name_AR as name');
            }
        )->get();
    }

    public function get_experience_level($id , string $lang , bool $to_array){
        $experience_level = ExperienceLevel::query()->when($lang == 'en' ,
            function($query) use($id){
                return $query->select('id' , 'name_EN as name')->where('id' , $id)->first();
            }
            ,
            function($query) use($id){
                return $query->select('id' , 'name_AR as name')->where('id' , $id)->first();
            }
        );
        if($to_array) return $experience_level ;
        return $experience_level->name;
    }
}
