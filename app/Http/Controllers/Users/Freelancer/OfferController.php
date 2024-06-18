<?php

namespace App\Http\Controllers\Users\Freelancer;

use App\Http\Controllers\BaseController;
use App\Http\Requests\OfferRequest;
use App\Models\Users\Freelancer\Offer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OfferController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->has('task_id')) {
            $offers = (new Offer)->get_all_offers($request->task_id, request('lang'));
            return $this->sendResponse($offers);
        }
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
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), (new OfferRequest)->rules());
            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            /** @var \App\Models\User $user */
            $user = Auth::user();
            if ($user->hasOffer($request->task_id)) {
                return $this->sendError(['message' => 'This user already offered']);
            }

            $request['user_id'] = Auth::id();
            $offer = Offer::create($request->all());

            DB::commit();
            return $this->sendResponse($offer);
        } catch (Exception $ex) {
            DB::rollBack();
            return $this->sendError($ex->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $offer = Offer::find($id);
        if (is_null($offer)) {
            return $this->sendError(['message' => 'There is no offer with this ID']);
        }

        $offer = $offer->get_offer($offer, request('lang'));
        return $this->sendResponse($offer);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Offer $offer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Offer $offer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $offer = Offer::find($id);
        if (is_null($offer)) {
            return $this->sendError(['message' => 'There is no offer with this ID']);
        }
        $offer->delete();
        return $this->sendResponse();
    }
}
