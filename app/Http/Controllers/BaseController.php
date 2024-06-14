<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function sendResponse($result = [], $data_name = "data")
    {
        $response = [
            'status' => 'success',
            $data_name => $result
        ];

        return response()->json($response, 200);
    }

    public function sendError($errorMessage = [], $code = 200)
    {
        $response = [
            'status' => 'failure'
        ];

        if(!empty($errorMessage))
        {
            $response['error_message'] = $errorMessage;
        }

        return response()->json($response, $code);
    }

    /**
     * @param Request $request
     * @param string $type
     * @return string
     */
    public function get_image(Request $request, string $type): string
    {
        $image_path = "";

        if($request->hasFile('image'))
        {
            $image = $request['image'] ;
            // check config/filesystem.php to know the meaning of public in the second parameter
            $image_path = $image->store($type , 'public');
        }

        return $image_path ;
    }
}
