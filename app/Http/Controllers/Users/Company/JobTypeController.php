<?php

namespace App\Http\Controllers\Users\Company;

use App\Http\Controllers\BaseController;
use App\Models\Users\Company\JobType;
use Illuminate\Http\Request;

class JobTypeController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lang = request('lang');
        $job_types = (new JobType)->get_all_job_types($lang);
        return $this->sendResponse($job_types);
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
