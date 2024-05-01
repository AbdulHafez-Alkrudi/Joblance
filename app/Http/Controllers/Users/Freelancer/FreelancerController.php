<?php

namespace App\Http\Controllers\Users\Freelancer;

use App\Http\Controllers\BaseController;
use App\Models\Freelancer;
use App\Models\User;
use App\Models\Major;
use Illuminate\Http\Request;

class FreelancerController extends BaseController
{
    public function index(string $lang)
    {
        $freelancers = Freelancer::all();

        foreach ($freelancers as $key => $freelancer)
        {
            $freelancers[$key] = $this->show($freelancer, $lang);
        }

        return $this->sendResponse($freelancers);
    }


    /**
     * Display the specified resource.
     */
    public function show(Freelancer $freelancer, string $lang)
    {
        return $this->sendResponse($freelancer);
        $major = Major::query()->find($freelancer->major_id);

        $freelancer_data = [
            'id' => $freelancer->id,
            'name' => $freelancer->first_name .' '. $freelancer->last_name,
            'image' => $freelancer->image,
            'bio'   => is_null($freelancer->bio) ? "" : $freelancer->bio,
            'major' => $lang == "EN" ? $major->name_EN : $major->name_AR,
            'location' =>$freelancer->location,
            'open_to_work' => $freelancer->open_to_work,
        ];

        return $freelancer_data;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $freelancer)
    {
        // the user may change something like the phone number which is not in the freelancer table, so I must retrieve
        // the user information from the User table that represents that freelancer

        if(array_key_exists('phone_number', $request->toArray()))
            User::where('userable_id' , $freelancer)->
                  where('userable_type' , Freelancer::class)->
                  update(['phone_number' => $request->phone_number]);

        $freelancer = Freelancer::find($freelancer)->update($request->except('phone_number'));
        return $this->sendResponse($freelancer);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($freelancer)
    {
        Freelancer::destroy($freelancer);
        return $this->sendResponse(true);
    }
}
