<?php

namespace App\Http\Controllers\Users\Company;

use App\Http\Controllers\BaseController;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lang = request('lang');
        $companies = (new Company)->get_all_companies($lang);
        return $this->sendResponse($companies);

    }
    /**
     * Display the specified resource.
     */
    public function show($company)
    {
        $company = User::find($company)->userable;

        $lang = request('lang');

        $company = (new Company)->get_info($company , $lang);
        return $this->sendResponse($company);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$company)
    {
        $company = User::find($company)->userable;

        // Check if there is any data related to the User table:
        $data = $request->all();
        if(array_key_exists('phone_number' , $data)){
            User::query()->where('userable_id' , $company->id)
                ->where('userable_type' , Company::class)
                ->update(['phone_number' => $request['phone_number']]);
        }
        if(array_key_exists('image' , $data)){

            Storage::disk('public')->delete($company->image);

            $path = $data['image']->store('company' , 'public');
            $data['image'] = $path ;
        }
        $data = array_diff_key($data , array_flip(['phone_number']));

        $company->update($data);
        return $this->sendResponse($company);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        if($company->image != null){
            Storage::disk('public')->delete($company->image);
        }
        $user = $company->user ;
        $user->delete();
        return $this->sendResponse();
    }
}
