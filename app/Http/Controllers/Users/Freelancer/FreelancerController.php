<?php

namespace App\Http\Controllers\Users\Freelancer;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Freelancer\FreelancerCollection;
use App\Models\Freelancer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FreelancerController extends BaseController
{
    public function index()
    {
        $lang = \request('lang');
        $freelancers = (new Freelancer)->get_all_freelancers($lang) ;
        $freelancers = new FreelancerCollection($freelancers);

        return $this->sendResponse($freelancers);
    }


    /**
     * Display the specified resource.
     */
    public function show($user_id)
    {
        $user = User::find($user_id);
        if(is_null($user)){
            return $this->sendError('there is no user with this ID');
        }
        return $this->sendResponse( (new Freelancer)->get_info($user->userable , \request('lang')) );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$freelancer)
    {
        $freelancer = User::find($freelancer);
        if(is_null($freelancer)){
            return $this->sendError('there is no user with this ID');
        }
        // the user may change something like the phone number which is not in the freelancer table, so I must retrieve
        // the user information from the User table that represents that freelancer
        $data = $request->all() ;
        if(array_key_exists('phone_number', $data))
            User::where('userable_id' , $freelancer->id)->
                  where('userable_type' , Freelancer::class)->
                  update(['phone_number' => $request->phone_number]);

        if(array_key_exists('image' , $data)){
            Storage::disk('public')->delete($freelancer->image);
            $data['image'] = $data['image']->store('freelancer' , 'public');
        }
        $data = array_diff_key($data , array_flip(['phone_number']));

        $freelancer->update($data);
        return $this->sendResponse($freelancer);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($user_id)
    {
        $user = User::find($user_id);
        if(is_null($user)){
            return $this->sendError('there is no user with this ID');
        }
        $freelancer = $user->userable;
        if($freelancer->image != null){
            Storage::disk('public')->delete($freelancer->image);
        }
        $user = $freelancer->user ;
        $user->delete();
        $freelancer->delete();
        return $this->sendResponse(true);
    }
}
