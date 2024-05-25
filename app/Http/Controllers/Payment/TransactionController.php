<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\BaseController;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\Transaction;
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
    public function index(Request $request)
    {
        if ($request->has('user_id')) {
            return $this->indexByUserId($request->user_id);
        }
        else if ($request->has('transaction_id')) {
            return $this->show($request->transaction_id);
        }

        $lang = \request('lang');
        $transactions = (new Transaction)->get_all_transactions($lang);
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
       // try {
            $storeTransactionRequest = new StoreTransactionRequest();
            $validator = Validator::make($request->all(), $storeTransactionRequest->rules());

            if ($validator->fails())
            {
                return $this->sendError($validator->errors());
            }

            // Verify if related records exist
            $transactionTypeExists = DB::table('transaction_types')->where('id', $request->transaction_type_id)->exists();
            $transactionStatusExists = DB::table('transaction_statuses')->where('id', $request->transaction_status_id)->exists();
            $userExists = DB::table('users')->where('id', Auth::id())->exists();

            if (!$transactionTypeExists || !$transactionStatusExists || !$userExists) {
                $errors = [];
                if (!$transactionTypeExists) {
                    $errors[] = 'Invalid transaction type ID';
                }
                if (!$transactionStatusExists) {
                    $errors[] = 'Invalid transaction status ID';
                }
                if (!$userExists) {
                    $errors[] = 'Invalid user ID';
                }
                return $this->sendError(['message' => $errors]);
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
        // } catch (Exception $ex) {
        //     DB::rollBack();
        //     return $this->sendError(['message' => $ex->getMessage()]);
        // }
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

    public function indexByUserId(string $userId)
    {
        if (is_null(User::find($userId))) {
            return $this->sendError('There is no user with this ID');
        }

        $transactions = Transaction::query()->where('user_id', $userId)->get();
        return $this->sendResponse($transactions);
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
