<?php

namespace App\Http\Controllers\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Srmklive\PayPal\Facades\PayPal;
use Srmklive\PayPal\Services\ExpressCheckout;
use Srmklive\PayPal\Services\PayPal as PayPalClient;


class PayPalController extends BaseController
{
    public function paypal(Request $request)
    {
        $validator = validator($request->all(), [
            'value' => 'required|min:0|max:9999999.99|numeric',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $value = $request->value;

        $data = [];
        $data['intent'] = 'CAPTURE';
        $data['purchase_units'] = [
            [
              'reference_id' => 1234,
              'amount' => [
                'currency_code' => 'USD',
                'value' => $value,
              ],
            ],
        ];

        $data['application_context'] = [
            'cancel_url' => route('paypal.cancel'),
            'return_url' => route('paypal.success'),
        ];

        $provider = new PayPalClient;
        $provider->getAccessToken();
        $response = $provider->createOrder($data);

        dd($response);
    }

    public function success(Request $request)
    {

    }

    public function cancel(Request $request)
    {

    }
}
