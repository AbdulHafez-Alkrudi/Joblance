<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProjectImage extends Model
{
    use HasFactory;
    protected $fillable = [
        'project_id',
        'image'
    ];
    public function UserProject(): BelongsTo
    {
        return $this->belongsTo(UserProject::class );
    }

    public function store($images , $project_id): array
    {
        $data = array();
        $cnt = 0 ;
        foreach($images as $array_image){
            $image = $array_image['image'];
            $image_name = (time() + $cnt++) .'.'.$image->getClientOriginalExtension();

            $path = 'images/UserProjects/' ;

            $image->move($path,$image_name);
            $image_name = $path.$image_name ;

            $final_data = ['project_id' => $project_id , 'image' => $image_name];
            $data[] = UserProjectImage::create($final_data);
        }
        return $data;
    }
}
