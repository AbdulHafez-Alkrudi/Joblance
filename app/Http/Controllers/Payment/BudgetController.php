<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\BaseController;
use App\Http\Requests\ChargeBudgetRequest;
use App\Http\Requests\StoreBudgetRequest;
use App\Models\Budget;
use App\Models\User;
use App\Models\Transaction;
use App\Models\TransactionStatus;
use App\Models\TransactionTypes;
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
        $budget = Budget::where('user_id', Auth::id())->first();

        return $this->sendResponse($budget);
    }

    public function charge(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'balance' => ['required', 'numeric', 'min:1']
            ]);

            if ($validator->fails())
            {
                return $this->sendError($validator->errors());
            }

            $lang = \request('lang');
            $transaction_status = TransactionStatus::query()->when($lang == 'en',
                function($query) {
                    return $query->select('id')->where('name_EN', 'complete');
                }
                ,
                function($query) {
                    return $query->select('id')->where('name_AR', 'مكتمل');
                }
            )->first();

            $transaction_type = TransactionTypes::query()->when($lang == 'en',
                function($query) {
                    return $query->select('id')->where('name_EN', 'recieve Cash');
                }
                ,
                function($query) {
                    return $query->select('id')->where('name_AR', 'تلقي نقداً');
                }
            )->first();

            $transaction_request = new Request($request->all());
            $transaction_request['transaction_type_id'] = $transaction_type->id;
            $transaction_request['transaction_status_id'] = $transaction_status->id;

            $transaction = (new TransactionController)->store($transaction_request);
            if ($transaction->getData()->status == 'failure') {
                return $transaction;
            }

            $user_budget = Budget::where('user_id', Auth::id())->first();
            $user_budget->update([
                'balance' => $user_budget->balance + $request->balance,
            ]);
            $user_budget->refresh();

            DB::commit();

            return $this->sendResponse($user_budget);
        } catch (Exception $ex) {
            DB::rollBack();
            return $this->sendError(['message' => $ex->getMessage()]);
        }
    }

    public function pay(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'balance' => ['required', 'numeric', 'min:1'],
                'user_id' => ['required', 'exists:users,id']
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            $my_budget = Budget::where('my_id', Auth::id())->first();
            if ($my_budget->balance < $request->balance) {
                return $this->sendError(['message' => "Your budget's balance less than request's balance"]);
            }

            $lang = \request('lang');
            $transaction_status = TransactionStatus::query()->when($lang == 'en',
                function($query) {
                    return $query->select('id')->where('name_EN', 'complete');
                }
                ,
                function($query) {
                    return $query->select('id')->where('name_AR', 'مكتمل');
                }
            )->first();

            $transaction_type = TransactionTypes::query()->when($lang == 'en',
                function($query) {
                    return $query->select('id')->where('name_EN', 'pay Cash');
                }
                ,
                function($query) {
                    return $query->select('id')->where('name_AR', 'دفع نقداً');
                }
            )->first();

            $transaction_request = new Request($request->all());
            $transaction_request['transaction_type_id'] = $transaction_type->id;
            $transaction_request['transaction_status_id'] = $transaction_status->id;

            $transaction = (new TransactionController)->store($transaction_request);
            if ($transaction->getData()->status == 'failure') {
                return $transaction;
            }

            $my_budget->update([
                'balance' => $my_budget->balance - $request->balance,
            ]);
            $my_budget->refresh();

            $user_budget = Budget::where('user_id', $request->user_id)->first();
            $user_budget->update([
                'balance' => $user_budget->balance + $request->balance,
            ]);

            DB::commit();

            return $this->sendResponse($my_budget);
        } catch (Exception $ex) {
            DB::rollBack();
            return $this->sendError(['message' => $ex->getMessage()]);
        }
    }
}
