<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\BaseController;
use App\Http\Requests\GetTransactionsRequest;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\Payment\Transaction;
use App\Models\Users\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index($userId, Request $request)
    {
        $getTransactions = new GetTransactionsRequest();
        $validator = Validator::make($request->all(), $getTransactions->rules());

        if ($validator->fails())
        {
            return $this->sendError($validator->errors());
        }

        $request['userId'] = $userId;
        if ($request->has('day')) {
            $transactions = (new Transaction)->getTransactionsForUserInDay($request, request('lang'));
        }
        else {
            $transactions = (new Transaction)->getTransactionsForUserInMonth($request, request('lang'));
        }

        return $this->sendResponse($transactions);
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
            $storeTransactionRequest = new StoreTransactionRequest();
            $validator = Validator::make($request->all(), $storeTransactionRequest->rules());

            if ($validator->fails())
            {
                return $this->sendError($validator->errors());
            }

            $transaction_data = [
                'balance' => $request->balance,
                'transaction_type_id'  => $request->transaction_type_id,
                'transaction_status_id' => $request->transaction_status_id,
                'user_id' => Auth::id(),
            ];

            $transaction = Transaction::create($transaction_data);

            DB::commit();

            return $this->sendResponse($transaction);
        } catch (Exception $ex) {
            DB::rollBack();
            return $this->sendError(['message' => $ex->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $lang = \request('lang');

        $transaction = Transaction::find($id);
        $transaction = $transaction->get_info($transaction, $lang);

        return $this->sendResponse($transaction);
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
        $transaction = Transaction::find($id);
        if (is_null($transaction)) {
            return $this->sendError(['message' => 'There is no transaction with this ID']);
        }

        $transaction->delete();
        return $this->sendResponse();
    }
}
