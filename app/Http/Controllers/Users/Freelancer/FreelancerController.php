<?php

namespace App\Http\Controllers\Users\Freelancer;

use App\Http\Controllers\BaseController;
use App\Models\Freelancer;
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Freelancer $freelancer, string $lang)
    {
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
     * Show the form for editing the specified resource.
     */
    public function edit(Freelancer $freelancer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Freelancer $freelancer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Freelancer $freelancer)
    {
        //
    }
}
