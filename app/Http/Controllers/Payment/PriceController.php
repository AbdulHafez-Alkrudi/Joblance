<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\BaseController;
use App\Http\Requests\PriceRequest;
use App\Models\Payment\Price;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PriceController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $prices = (new Price)->get_all_prices(request('lang'));
        return $prices;
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
        $validator = Validator::make($request->all(), (new PriceRequest)->rules());
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $price = Price::create($request->all());
        return $this->sendResponse($price);
    }

    /**
     * Display the specified resource.
     */
    public function show(Price $price)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Price $price)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $price)
    {
        $price = Price::find($price);
        if (is_null($price)) {
            return $this->sendError(['message' => 'There is no price with this ID']);
        }
        //return $request->input();
        $price->update($request->all());
        return $this->sendResponse($price);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $price = Price::find($id);
        if (is_null($price)) {
            return $this->sendError(['message' => 'There is no price with this ID']);
        }

        $price->delete();
        return $this->sendResponse();
    }
}
