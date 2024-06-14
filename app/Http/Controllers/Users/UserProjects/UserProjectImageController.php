<?php

namespace App\Http\Controllers\Users\UserProjects;

use App\Http\Controllers\BaseController;
use App\Models\Users\UserProjects\UserProjectImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserProjectImageController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($data , $project_id): JsonResponse|array
    {
        $validator = Validator::make($data , [
            'images' => ['array' , 'present'],
            'images.*' => ['image' , 'mimes:jpeg,png,bmp,jpg,gif,svg']
        ]);
        if($validator->fails())
        {
            $response['status'] = 'failure' ;
            $response['error_message'] = $validator->errors() ;
            return $response ;
        }
        $response['status'] ='success' ;
        $response['images'] = (new UserProjectImage)->store($data['images'] , $project_id);
        return $response;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $project_image = UserProjectImage::find($id) ;
        if($project_image == null){
            return $this->sendError('there is no image with the provided ID');
        }
        // before I delete the image from the database I should remove it from the public directory:
        Storage::disk('public')->delete($project_image->image_path);
        $project_image->delete() ;
    }
}
