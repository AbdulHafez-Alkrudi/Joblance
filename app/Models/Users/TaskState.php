<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskState extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_EN',
        'name_AR'
    ];

    public function get_task_state($id , string $lang , bool $to_array){
        $task_state = TaskState::query()->when($lang == 'en' ,
            function($query) use($id){
                return $query->select('id', 'name_EN as name')->where('id' , $id)->first();
            }
            ,
            function($query) use($id){
                return $query->select('id', 'name_AR as name')->where('id' , $id)->first();
            }
        );
        if($to_array) return $task_state ;
        return $task_state->name;
    }
}
