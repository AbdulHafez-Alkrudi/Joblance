<?php

namespace App\Http\Controllers\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\Budget;
use App\Models\PayPalOrder;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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

        $data = [];
        $data['intent'] = 'CAPTURE';
        $data['purchase_units'] = [
            [
              'reference_id' => 1234,
              'amount' => [
                'currency_code' => 'USD',
                'value' => $request->value,
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

        try {
            return $this->sendResponse(['redirect_link' => $response['links'][1]['href']]);
        } catch (Exception $ex) {
            return $this->sendError(['message' => 'something went wrong']);
        }
    }

    public function success(Request $request)
    {
        try {
            $provider = new PayPalClient;
            $provider->getAccessToken();
            $response = $provider->capturePaymentOrder($request->get('token'));

            if ($response['status'] === 'COMPLETED')
            {
                // Payment was successful
                // Process the payment
                // Redirect to the thank you page
                $order_id = $response['id'];
                $user_id = Auth::id();

                if (!PayPalOrder::where('user_id', $user_id)->where('order_id', $order_id)->exists()) {
                    return $this->sendError(['errors' => ['Not Found' => ['message' => 'no such an order id']]]);
                }

                $paypal_order = PaypalOrder::where('user_id', $user_id)->where('order_id', $order_id)->first();
                if ($user_id !== $paypal_order->user_id) {
                    return $this->sendError(['errors' => ['Unauthorized' => ['message' => 'You can not use this link']]]);
                }

                $user_budget = Budget::query()->where('user_id', $user_id)->first();
                Budget::query()->find($user_budget->id)->update([
                    'balance' => $user_budget->balance + $paypal_order->amount,
                ]);


            }
            else
            {

            }
        } catch (Exception $ex) {
            return $this->sendError(['message' => 'Invalid token']);
        }
    }

    public function cancel(Request $request)
    {

    }

    public static function get_after_paypal_fee($amount)
    {
        return round($amount + ($amount * (3.49 / 100.00)) + 0.5, 2);
    }

    public function charge_my_wallet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => ['required', 'numeric', 'min:1', 'max:90000.00'],
        ]);

        if ($validator->fails())
        {
            return $this->sendError($validator->errors());
        }

        $request['buy'] = 0;
        PaypalOrder::where('user_id', Auth::id())->delete();
        return $this->charge_using_paypal($request);
    }

    public function charge_using_paypal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => ['required', 'numeric', 'min:1', 'max:90000.00'],
            'buy' => ['required', 'boolean']
        ]);

        if ($validator->fails())
        {
            return $this->sendError($validator->errors());
        }

        $amount = PayPalController::get_after_paypal_fee($request->amount);

        $data = [];
        $data['intent'] = 'CAPTURE';
        $data['purchase_units'] = [
            [
                'amount' => [
                'currency_code' => 'USD',
                'value' => $amount,
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

        try {
            if ($response['status'] === 'CREATED')
            {
                $order_id = $response['id'];
                $user_id  = Auth::id();
                PaypalOrder::create([
                    'order_id' => $order_id,
                    'user_id' => $user_id,
                    'amount' => $request->amount,
                    'buy' => $request->buy,
                ]);
                $res['payment_link'] =  $response['links'][1]['href'];
                $res['success_link'] = route('paypal.success');
                $res['cancel_link'] = route('paypal.cancel');

                return $this->sendResponse($res);
            }
            else {
                return $this->sendError(['message' => 'Unknown error']);
            }
        } catch (Exception $ex) {
            return $this->sendError(['message' => 'Unknown error']);
        }
    }

    public function pay_using_paypal(Request $request)
    {
        DB::beginTransaction();
        try {
            PayPalOrder::query()->where('user_id', Auth::id())->delete();

            $response = $this->charge_using_paypal($request);

            DB::commit();
            
            return $response;
        } catch (Exception $ex) {
            DB::rollBack();
            return $this->sendError(['message' => $ex->getMessage()]);
        }
    }
}
