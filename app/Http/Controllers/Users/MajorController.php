<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Major;
use Illuminate\Http\Request;
use phpseclib3\Math\PrimeField\Integer;

class MajorController extends BaseController
{
    public function index()
    {
        $majors = (new Major)->get_all_majors(request('lang'));
        return $this->sendResponse($majors);
    }


    /**
     * Display the specified resource.
     */
    public function show($major_id)
    {

        $major = (new Major)->get_major($major_id , \request('lang') , true);
        return $this->sendResponse($major);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $freelancer)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Major $major)
    {

        return $this->sendResponse(true);
    }
}
