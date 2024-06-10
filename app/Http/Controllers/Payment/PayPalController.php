<?php

namespace App\Http\Controllers\Payment;

use App\Events\PayoutEmail;
use App\Events\SendPayoutEmail as EventsSendPayoutEmail;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Payment\BudgetController;
use App\Jobs\CheckPayoutStatus;
use App\Mail\SendPayoutEmail;
use App\Models\Budget;
use App\Models\PayPalOrder;
use App\Models\Transaction;
use App\Models\TransactionStatus;
use App\Models\TransactionTypes;
use App\Models\User;
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
                    return $this->sendError(['message' => 'no such an order id']);
                }

                $paypal_order = PaypalOrder::where('user_id', $user_id)->where('order_id', $order_id)->first();
                if ($user_id !== $paypal_order->user_id) {
                    return $this->sendError(['errors' => ['Unauthorized' => ['message' => 'You can not use this link']]]);
                }

                $user_budget = Budget::query()->where('user_id', $user_id)->first();
                Budget::query()->find($user_budget->id)->update([
                    'balance' => $user_budget->balance + $paypal_order->amount,
                ]);

                PayPalOrder::find($paypal_order->id)->delete();
                if (!$paypal_order->buy)
                {
                    $user_budget->refresh();
                    $charge_transaction = TransactionTypes::where('name', 'charge via PayPal')->first();
                    $complete_status = TransactionStatus::where('name', 'complete')->first();
                    Transaction::create([
                        'balance' => $paypal_order->amount,
                        'transactions_type_id' => $charge_transaction->id,
                        'transaction_status_id' => $complete_status->id,
                        'user_id' => $user_id,
                    ]);

                    return $this->sendResponse($user_budget);
                }
                else
                {
                    $pay_response = (new BudgetController())->try_to_pay($paypal_order->amount);
                    return $pay_response;
                }
            }
            else
            {
                return $this->sendError(['message' => 'this operation didn\'t completed yet']);
            }
        } catch (Exception $ex) {
            return $this->sendError(['message' => 'Invalid token']);
        }
    }

    public function cancel(Request $request)
    {
        try {
            $order_id = $request->get('token');
            $user_id  = Auth::id();

            if (!PayPalOrder::where('user_id', $user_id)->where('order_id', $order_id)->exists()) {
                return $this->sendError(['message' => 'no such an order id']);
            }

            $paypal_order = PaypalOrder::where('user_id', $user_id)->where('order_id', $order_id)->first();
            PaypalOrder::where('user_id', $user_id)->where('order_id', $order_id)->delete();

            if (!$paypal_order->buy)
            {
                $user_budget = Budget::query()->where('user_id', $user_id)->first();
                return $this->sendResponse($user_budget);
            }
            else
            {
                return $this->sendResponse();
            }
        } catch (Exception $ex) {
            return $this->sendError(['message' => 'Invalid token']);
        }
    }

    public static function generate_random_string($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }


    public function send(Request $request)
    {
        DB::beginTransaction();
        try {
            $user_budget = Budget::query()->where(Auth::id())->first();
            $minimum = 1;
            if ($user_budget->balance < $minimum)
            {
                return $this->sendError(['message' => 'Your wallet is almost empty, no money to be withdrown']);
            }

            $validator = validator($request->all(), [
                'email' => ['required', 'email'],
                'value' => ['required', 'min:1', 'max:' . min($user_budget->balance, 20000), 'numeric'],
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            $provider = new PayPalClient;
            $provider->getAccessToken();

            $user_id = Auth::id();
            $net_value = $request->value;
            $value_to_send = round($net_value - (($net_value * 2 / 100.00) + 0.05), 2);
            $receiver_email = $request->email;

            //create a pending tranaction
            $transaction_type = TransactionTypes::where('name', 'recieve via PayPal')->first();
            $transaction_status = TransactionStatus::where('name', 'pending')->first();
            $transaction = Transaction::create([
                'balance' => $value_to_send,
                'transactions_type_id' => $transaction_type->id,
                'transaction_status_id' => $transaction_status->id,
                'user_id' => $user_budget->id,
            ]);

            $data = [
                'sender_batch_header' => [
                    'sender_batch_id' => PayPalController::generate_random_string(10) . '-' . $transaction->id,
                    'email_subject' => 'You have a payout!',
                    'email_message' => 'You have received a payout! Thanks for using our service!'
                ],
                'items' => [
                    0 => [
                        'recipient_type' => 'EMAIL',
                        'amount' => [
                            'value' => $value_to_send,
                            'currency' => 'USD'
                        ],
                        'note' => 'Thanks for using our app!',
                        'sender_item_id' => $user_id,
                        'receiver' => $receiver_email
                    ]
                ]
            ];

            $response = $provider->createBatchPayout($data);
            if (array_key_exists("error", $response)) {
                DB::rollBack();
                return $this->sendError(['message' => $response['error']['name'] . ' try again later']);
            }
            if ($response['batch_header']['batch_status'] === 'DENIED') {
                return $this->sendError(['Denied' => ['message' => 'Your request has been denied from paypal']]);
            }

            Transaction::find($transaction->id)->update([
                'code' => $response['batch_header']['payout_batch_id']
            ]);
            $transaction->refresh();

            Budget::find($user_budget->id)->update([
                'balance' => $user_budget->balance - $net_value,
                'freeze_balance' => $user_budget->freeze_balance + $net_value,
            ]);
            $user_budget->refresh();

            DB::commit();

            if ($response['batch_header']['batch_status'] === 'SUCCESS') {
                return $this->success_payout($response['batch_header']['payout_batch_id']);
            }

            if ($response['batch_header']['batch_status'] === 'CANCELED') {
                return $this->cancel_payout($response['batch_header']['payout_batch_id']);
            }

            // if the status is PENDING or PROCESSING
            CheckPayoutStatus::dispatch($transaction->transaction_id)->delay(now()->addMinutes(5));

            return response()->json([
                'status'  => 'success',
                'message' => 'Pending, a message will be sent to your email',
                'data'    => $user_budget,
            ], 200);
        } catch (Exception $ex) {
            DB::rollBack();
            return $this->sendError(['Forbidden' => ['message' => 'This service is unavailable in your country']]);
        }
    }

    public function success_payout($payout_id)
    {
        DB::beginTransaction();
        try {
            $provider = new PayPalClient;
            $provider->getAccessToken();
            $response = $provider->showBatchPayoutDetails($payout_id);

            if ($response['batch_header']['batch_status'] !== 'SUCCESS') {
                return $this->sendError(['message' => 'This payout did not seccess yet']);
            }

            $sender_batch_id = $response['batch_header']['sender_batch_header']['sender_batch_id'];
            $receiver = $response['items'][0]['payout_item']['receiver'];
            $net_amount = $response['batch_header']['amount']['value'];
            $total_amount = $response['batch_header']['fees']['value'] + $net_amount;

            $transaction_id = substr($sender_batch_id, 11, strlen($sender_batch_id) - 11);
            $transaction = Transaction::find($transaction_id);

            $user = User::find($transaction->user_id);
            $user_budget = $user->budget;
            Budget::find($user_budget->id)->update([
                'freeze_balance' => $user_budget->freeze_balance - $total_amount,
            ]);
            $user_budget->refresh();

            $transaction_status = TransactionStatus::where('name', 'pending')->first();
            if ($transaction->transaction_status_id !== $transaction_status->id) {
                return $this->sendError(['message' => 'this transaction has been processed before']);
            }

            $transaction_status = TransactionStatus::where('name', 'complete')->first();
            Transaction::find($transaction_id)->update(['transaction_status_id' => $transaction_status->id]);

            $message_body = 'Check your paypal account, the payout completed successfully';
            $message_subject = 'Payout';

            DB::commit();

            PayoutEmail::dispatch($user, $message_body, $message_subject);

            return $this->sendResponse($user_budget);
        } catch (Exception $ex) {
            DB::rollBack();
            return $this->sendError(['Forbidden' => ['message' => 'This service is unavailable in your country']]);
        }
    }

    public function cancel_payout($payout_id)
    {
        DB::beginTransaction();
        try {
            $provider = new PayPalClient;
            $provider->getAccessToken();
            $response = $provider->showBatchPayoutDetails($payout_id);

            if ($response['batch_header']['batch_status'] !== 'CANCELED' && $response['batch_header']['batch_status'] !== 'DENIED') {
                return $this->sendError(['message' => 'This payout did not canceled yet']);
            }

            $sender_batch_id = $response['batch_header']['sender_batch_header']['sender_batch_id'];
            $receiver = $response['items'][0]['payout_item']['receiver'];
            $net_amount = $response['batch_header']['amount']['value'];
            $total_amount = $response['batch_header']['fees']['value'] + $net_amount;

            $transaction_id = substr($sender_batch_id, 11, strlen($sender_batch_id) - 11);
            $transaction = Transaction::find($transaction_id);

            $user = User::find($transaction->user_id);
            $user_budget = $user->budget;
            Budget::find($user_budget->id)->update([
                'freeze_balance' => $user_budget->freeze_balance - $total_amount,
                'balance' => $user_budget->balance + $total_amount
            ]);

            $transaction_status = TransactionStatus::where('name', 'pending')->first();
            if ($transaction->transaction_status_id !== $transaction_status->id) {
                return $this->sendError(['message' => 'this transaction has been processed before']);
            }

            $transaction_status = TransactionStatus::where('name', 'cancle')->first();
            Transaction::find($transaction_id)->update(['transaction_status_id' => $transaction_status->id]);

            $message_body = 'Check your paypal account, the payout cancled successfully';
            $message_subject = 'Payout';

            DB::commit();

            PayoutEmail::dispatch($user, $message_body, $message_subject);

            return $this->sendResponse();
        } catch (Exception $ex) {
            return $this->sendError(['Forbidden' => ['message' => 'This service is unavailable in your country']]);
        }
    }

    public static function get_after_paypal_fee($amount)
    {
        return round($amount + ($amount * (3.49 / 100.00)) + 0.5, 2);
    }

    public function charge_my_wallet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        if ($validator->fails())
        {
            return $this->sendError($validator->errors());
        }

        PaypalOrder::where('user_id', Auth::id())->delete();
        
        return $this->charge_using_paypal($request);
    }

    public function charge_using_paypal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => ['required', 'numeric', 'min:1']
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

        DB::beginTransaction();
        try {
            if ($response['status'] === 'CREATED')
            {
                PaypalOrder::create([
                    'order_id' => $response['id'],
                    'user_id' => Auth::id(),
                    'amount' => $request->amount,
                    'buy' => $request->buy,
                ]);

                $res['payment_link'] =  $response['links'][1]['href'];
                $res['success_link'] = route('paypal.success');
                $res['cancel_link'] = route('paypal.cancel');

                DB::commit();

                return $this->sendResponse($res);
            }
            else {
                return $this->sendError(['message' => 'Unknown error']);
            }
        } catch (Exception $ex) {
            DB::rollBack();
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
