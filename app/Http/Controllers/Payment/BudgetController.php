<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\BaseController;
use App\Http\Requests\ChargeBudgetRequest;
use App\Http\Requests\StoreBudgetRequest;
use App\Models\Payment\Budget;
use App\Models\User;
use App\Models\Payment\Transaction;
use App\Models\Payment\TransactionStatus;
use App\Models\Payment\TransactionTypes;
use App\Notifications\UserNotification;
use Svg\Tag\Rect;

;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BudgetController extends BaseController
{
    public function get_budget()
    {
        return $this->sendResponse(Auth::user()->budget()->select('balance')->first());
    }

    public function charge(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'balance' => ['required', 'numeric', 'min:1'],
                'user_id' => ['required', 'exists:users,id']
            ]);

            if ($validator->fails())
            {
                return $this->sendError($validator->errors());
            }

            $transaction_status = TransactionStatus::query()->select('id')->where('name_EN', 'complete')->first();
            $transaction_type = TransactionTypes::query()->select('id')->where('name_EN', 'recieve Cash')->first();

            $transaction_request = new Request($request->all());
            $transaction_request['user_id'] = $request->user_id;
            $transaction_request['transaction_type_id'] = $transaction_type->id;
            $transaction_request['transaction_status_id'] = $transaction_status->id;

            $transaction = (new TransactionController)->store($transaction_request);
            if ($transaction->getData()->status == 'failure') {
                return $transaction;
            }

            $user_budget = Budget::where('user_id', $request->user_id)->first();
            $user_budget->update([
                'balance' => $user_budget->balance + $request->balance,
            ]);

            DB::commit();

            // To notify the user
            $user = User::find($request->user_id);
            $user->notify(new UserNotification('Charge Budget', 'Your wallet has been charged with '. $request->balance, []));

            return $this->sendResponse($user_budget);
        } catch (Exception $ex) {
            return $this->sendError(['message' => $ex->getMessage()]);
        }
    }

    public function pay(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'balance' => ['required', 'numeric', 'min:1']
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            $user_budget = Budget::where('user_id', Auth::id())->first();
            if ($user_budget->balance < $request->balance) {
                return $this->sendError(['message' => "Your budget's balance less than request's balance"]);
            }

            $transaction_status = TransactionStatus::query()->where('name_EN', 'complete')->first();
            $transaction_type = TransactionTypes::query()->where('name_EN', 'pay Cash')->first();

            $transaction_request = new Request($request->all());
            $transaction_request['user_id'] = Auth::id();
            $transaction_request['transaction_type_id'] = $transaction_type->id;
            $transaction_request['transaction_status_id'] = $transaction_status->id;

            $transaction = (new TransactionController)->store($transaction_request);
            if ($transaction->getData()->status == 'failure') {
                return $transaction;
            }

            $user_budget->update([
                'balance' => $user_budget->balance - $request->balance,
            ]);

            DB::commit();

            return $this->sendResponse($user_budget);
        } catch (Exception $ex) {
            DB::rollBack();
            return $this->sendError(['message' => $ex->getMessage()]);
        }
    }
}
