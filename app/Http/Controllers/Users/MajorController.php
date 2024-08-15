<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\BaseController;
use App\Http\Requests\StoreMajorRequest;
use App\Models\Users\Major;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use phpseclib3\Math\PrimeField\Integer;
use function PHPUnit\Framework\isNull;

class MajorController extends BaseController
{
    public function index()
    {
        $majors = (new Major)->get_all_majors(request('lang'));
        foreach($majors as $major){
           $major->image = $major->image != null ? asset('storage/' . $major->image) : "";
        }
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
                'image'   => $this->get_image($request, "Majors_Pic")
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
        //
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
