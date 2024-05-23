<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\BaseController;
use App\Http\Requests\ChargeBudgetRequest;
use App\Http\Requests\StoreBudgetRequest;
use App\Models\Budget;
use App\Models\Transaction;
use App\Models\TransactionStatus;
use App\Models\TransactionTypes;

;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BudgetController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
            $storeBudgetRequest = new StoreBudgetRequest();
            $validator = Validator::make($request->all(), $storeBudgetRequest->rules());

            if ($validator->fails())
            {
                return $this->sendError($validator->errors());
            }

            $budget_data = [
                'user_id' => $request->user_id,
                'balance' => is_null($request->balance) ? 0 : $request->balance,
            ];

            $budget = Budget::create($budget_data);

            DB::commit();

            return $this->sendResponse($budget);
        } catch (Exception $ex) {
            return $this->sendError(['message' => 'unknown error']);
        }
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

    public function chargeBudget(Request $request)
    {
        DB::beginTransaction();
        try {
            $chargeBudgetRequest = new ChargeBudgetRequest();
            $validator = Validator::make($request->all(), $chargeBudgetRequest->rules());

            if ($validator->fails())
            {
                return $this->sendError($validator->errors());
            }

            $budget = Budget::find($request->budget_id);
            if (Auth::id() != $budget->user_id) {
                return $this->sendError(['message' => 'you can not charge a wallet']);
            }

            $transaction_type    = TransactionTypes::query()->where('name', 'recieve Cash')->first();
            $transaction_status  = TransactionStatus::query()->where('name', 'complete')->first();

            $transaction_request = new Request($request->all());
            $transaction_request['transaction_type_id'] = $transaction_type->id;
            $transaction_request['transaction_status_id'] = $transaction_status->id;

            $transaction = (new TransactionController)->store($transaction_request);
            if ($transaction->getData()->status == 'failure') {
                return $transaction;
            }

            $budget->update([
                'balance' => $budget->balance + $request->value,
            ]);
            $budget->refresh();

            DB::commit();

            return $this->sendResponse($budget);
        } catch (Exception $ex) {
            return $this->sendError(['message' => $ex->getMessage()]);
        }
    }

    public function pay_using_my_wallet($amount)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            $user_budget = Budget::query()->where('user_id', $user->id);

            Budget::create([
                'balance' => $user_budget->balance - $amount,
                'freeze_balance' => $user_budget->freeze_balance + $amount
            ]);

            DB::commit();

            return $this->sendResponse();
        } catch (Exception $ex) {
            DB::rollBack();
            return $this->sendError(['message' => $ex->getMessage()]);
        }
    }

    public function try_to_pay($amount)
    {
        DB::beginTransaction();
        try {
            $response = ($this->pay_using_my_wallet($amount));
            if ($response->getData()->success)
            {
                DB::commit();
                $budget = Budget::query()->where('user_id', Auth::id())->first();

                return $this->sendResponse($budget);
            }

            DB::commit();
            return $response;
        } catch (Exception $ex) {
            return $this->sendError(['message' => $ex->getMessage()]);
        }
    }
}
