<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMajorRequest;
use App\Models\Major;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use phpseclib3\Math\PrimeField\Integer;

class MajorController extends BaseController
{
    public function index()
    {
        $majors = (new Major)->get_all_majors(request('lang'));
        return $this->sendResponse($majors);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $storeMajor = new StoreMajorRequest();
            $validator = Validator::make($request->all(), $storeMajor->rules());

            if ($validator->fails())
            {
                return $this->sendError($validator->errors());
            }

            $major = Major::create([
                'name_EN' => $request->name_EN,
                'name_AR' => $request->name_AR,
                'image'   => $request->image
            ]);

            DB::commit();

            return $this->sendResponse($major);
        } catch (Exception $ex) {
            DB::rollBack();
            return $this->sendError($ex->getMessage());
        }
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
        $major->delete();
        return $this->sendResponse();
    }
}
