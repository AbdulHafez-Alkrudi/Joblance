<?php

namespace App\Models\Users\Company;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Remote extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_EN', 'name_AR'
    ];

    public function job_details(): HasMany
    {
        return $this->hasMany(JobDetail::class);
    }

    public function get_remote($id , string $lang , bool $to_array){
        $remote = Remote::query()->when($lang == 'en' ,
            function($query) use($id){
                return $query->select('id' , 'name_EN as name')->where('id' , $id)->first();
            }
            ,
            function($query) use($id){
                return $query->select('id' , 'name_AR as name')->where('id' , $id)->first();
            }
        );
        if($to_array) return $remote ;
        return $remote->name;
    }

    public function get_all_remotes(string $lang): Collection|array
    {
        return Remote::query()->when($lang == 'en' ,
            function($query){
                return $query->select('id','name_EN as name');
            },
            function($query){
                return $query->select('id','name_AR as name');
            }
        )->get();
    }
}
