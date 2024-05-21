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
        'image_path'
    ];
    public function UserProject(): BelongsTo
    {
        return $this->belongsTo(UserProject::class );
    }

    public function store($images , $project_id): array
    {
        $data = array();
        if(!is_array($images)){
            // if I sent a single image, I'll convert it to an array
            $images = [$images] ;
        }
        foreach($images as $image){
            $path = $image->store('project_images' , 'public');
            $final_data = ['project_id' => $project_id , 'image_path' => $path];
            $data[] = UserProjectImage::create($final_data);
        }
        return $data;
    }
}
