<?php

namespace App\Models\Users\Freelancer;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudyCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_EN', 'name_AR'
    ];

    public function freelancers() : HasMany
    {
        return $this->hasMany(Freelancer::class);
    }

    public function get_study_case($id , string $lang , bool $to_array){
        $study_case = StudyCase::query()->when($lang == 'en' ,
            function($query) use($id){
                return $query->select('id' , 'name_EN as name')->where('id' , $id)->first();
            }
            ,
            function($query) use($id){
                return $query->select('id' , 'name_AR as name')->where('id' , $id)->first();
            }
        );
        if($to_array) return $study_case ;
        return $study_case->name;
    }

    public function get_all_study_case(string $lang): Collection|array
    {
        return StudyCase::query()->when($lang == 'en' ,
            function($query){
                return $query->select('id','name_EN as name');
            },
            function($query){
                return $query->select('id','name_AR as name');
            }
        )->get();
    }
}
