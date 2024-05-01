<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
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
    public function get_major($id , string $lang , bool $to_array){
        $major = Major::query()->when($lang == 'EN' ,
            function($query) use($id){
                return $query->select('id' , 'name_EN as name')->where('id' , $id)->first();
            }
            ,
            function($query) use($id){
                return $query->select('id' , 'name_AR as name')->where('id' , $id)->first();
            }
        );
        if($to_array) return $major ;
        return $major->name;
    }

    public function get_all_majors(string $lang): Collection|array
    {
        return Major::query()->when($lang == 'EN' ,
            function($query){
                return $query->select('id','name_EN as name');
            },
            function($query){
                return $query->select('id','name_AR as name');
            }
        )->get();
    }

}
