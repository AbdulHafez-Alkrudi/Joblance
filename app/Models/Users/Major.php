<?php

namespace App\Models\Users;

use App\Models\Users\Company\Company;
use App\Models\Users\Company\JobDetail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Major extends Model
{
    use HasFactory;
    protected $fillable = ['name_EN' , 'name_AR', 'image'] ;

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }
    //
    public function tasks() : HasMany
    {
        return $this->hasMany(Task::class);
    }
    public function job_details(): HasMany
    {
        return $this->hasMany(JobDetail::class);
    }
    public function get_major($id , string $lang , bool $to_array){
        $major = Major::query()->when($lang == 'en' ,
            function($query) use($id){
                return $query->select('id' , 'name_EN as name' , 'image')->where('id' , $id)->first();
            }
            ,
            function($query) use($id){
                return $query->select('id' , 'name_AR as name' , 'image')->where('id' , $id)->first();
            }
        );
        if($to_array) return $major ;
        return $major->name;
    }

    public function get_all_majors(string $lang): Collection|array
    {
        return Major::query()->when($lang == 'en' ,
            function($query){
                return $query->select('id','name_EN as name' , 'image');
            },
            function($query){
                return $query->select('id','name_AR as name' , 'image');
            }
        )->get();
    }
}
