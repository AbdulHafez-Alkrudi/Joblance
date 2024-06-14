<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\BaseController;
use App\Http\Requests\ChargeBudgetRequest;
use App\Http\Requests\StoreBudgetRequest;
use App\Models\Payment\Budget;
use App\Models\Users\User;
use App\Models\Payment\Transaction;
use App\Models\Payment\TransactionStatus;
use App\Models\Payment\TransactionTypes;
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $budget = Budget::find($id);

        if ($budget->user_id != Auth::id()) {
            return $this->sendError(['message' => 'you can not access this budget']);
        }

        return $this->sendResponse($budget);
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
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'balance' => ['nullable', 'numeric', 'min:0']
            ]);

            if ($validator->fails())
            {
                return $this->sendError($validator->errors());
            }

            $budget = Budget::find($id);
            $budget->update([
                'balance' => is_null($request->balance) ? 0 : $request->balance,
            ]);
            $budget->refresh();

            DB::commit();

            return $this->sendResponse($budget);
        } catch (Exception $ex) {
            return $this->sendError(['message' => $ex->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $budget = Budget::find($id);
        if (is_null($budget)) {
            return $this->sendError(['message' => 'There is no budget with this ID']);
        }

        $budget->delete();
        return $this->sendResponse();
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
            $user_budget = Budget::where('user_id', Auth::id())->first();

            $type_name = $lang == 'en' ? 'recieve Cash' : 'تلقي نقداً';
            $transaction_type = (new TransactionTypes)->get_transaction_type($type_name, $lang, 1);

            $status_name = $lang == 'en' ? 'complete' : 'مكتمل';
            $transaction_status = (new TransactionStatus)->get_transaction_status($status_name, $lang, 1);

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

            $lang = \request('lang');
            $user_budget = Budget::where('user_id', Auth::id())->first();

            if ($user_budget->balance < $request->balance) {
                return $this->sendError(['message' => "Your budget's balance less than request's balance"]);
            }

            $type_name = $lang == 'en' ? 'charge Cash' : 'دفع نقداً';
            $transaction_type    = (new TransactionTypes)->get_transaction_type($type_name, $lang, 1);

            $status_name = $lang == 'en' ? 'complete' : 'مكتمل';
            $transaction_status  = (new TransactionStatus)->get_transaction_status($status_name, $lang, 1);

            $transaction_request = new Request($request->all());
            $transaction_request['transaction_type_id'] = $transaction_type->id;
            $transaction_request['transaction_status_id'] = $transaction_status->id;

            $transaction = (new TransactionController)->store($transaction_request);
            if ($transaction->getData()->status == 'failure') {
                return $transaction;
            }

            $user_budget->update([
                'balance' => $user_budget->balance - $request->balance,
            ]);
            $user_budget->refresh();

            DB::commit();

            return $this->sendResponse($user_budget);
        } catch (Exception $ex) {
            DB::rollBack();
            return $this->sendError(['message' => $ex->getMessage()]);
        }
    }
}
