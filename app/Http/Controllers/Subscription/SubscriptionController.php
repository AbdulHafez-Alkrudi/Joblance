<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Payment\BudgetController;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\TransactionStatus;
use App\Models\TransactionTypes;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends BaseController
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
        $validator = Validator::make($request->all(), [
            'transaction_id' => ['required', 'exists:transactions,id']
        ]);

        if ($validator->fails())
        {
            return $this->sendError($validator->errors());
        }

        $user = User::find(Auth::id());
        if ($user->hasActiveSubscription()) {
            return $this->sendError(['message' => 'This user already Subscriped']);
        }

        // Delete all old Subscriptions that user had before
        Subscription::query()->where('user_id', $user->id)->delete();

        $transaction = Transaction::find($request->transaction_id);
        $transaction_status_name = (new TransactionStatus)->get_transaction_status($transaction->transaction_status_id, 'en', 0);
        if ($transaction_status_name != 'complete') {
            return $this->sendError(['message' => 'transaction_status is not complete']);
        }

        // Create a new Subscription
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'starts_at' => Carbon::now(),
            'ends_at' => Carbon::now()->addYear()
        ]);

        return $this->sendResponse($subscription);
    }

    /**
     * Display the specified resource.
     */
    public function show(Subscription $subscription)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subscription $subscription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subscription $subscription)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        $subscription = Subscription::where('user_id', Auth::id())->first();
        if (is_null($subscription)) {
            return $this->sendError(['message' => 'this user is not subscriber']);
        }

        $subscription->delete();
        return $this->sendResponse();
    }
}
