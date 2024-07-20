<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\BaseController;
use App\Http\Requests\GetTransactionsRequest;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\Payment\Transaction;
use App\Models\Payment\TransactionTypes;
use App\Models\User;
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
        $validator = Validator::make($request->all(), (new GetTransactionsRequest)->rules());
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $request['userId'] = $userId;
        if ($request->has('day')) {
            $transactions = (new Transaction)->getTransactionsForUserInDay($request, request('lang'));
        }
        else {
            $transactions = (new Transaction)->getTransactionsForUserInMonth($request, request('lang'));
        }

        $transaction_type = TransactionTypes::query()->where('name_EN', 'recieve Cash')->select('id')->first();
        $transaction_type2 = TransactionTypes::query()->where('name_EN', 'pay Cash')->select('id')->first();
        
        $totalRecieveCash = $transactions->where('transaction_type_id', $transaction_type->id)->pluck('balance')->sum();
        $totalPayCash    = $transactions->where('transaction_type_id', $transaction_type2->id)->pluck('balance')->sum();

        $data = [
            'transaction' => $transactions,
            'total_recieve_cash' => $totalRecieveCash,
            'total_pay_cash' => $totalPayCash
        ];

        return $this->sendResponse($data);
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
                'user_id' => $request->user_id
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
        //
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
        //
    }
}
