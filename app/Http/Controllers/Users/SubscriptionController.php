<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Payment\BudgetController;
use App\Models\Payment\Price;
use App\Models\User;
use App\Models\Users\Subscription;
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
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if ($user->hasActiveSubscription()) {
            $subscription = $user->subscription;
            $subscription = $subscription->get_info($subscription);
            return $this->sendResponse($subscription);
        }
        else {
            return $this->sendError('This user does not have active subscription');
        }
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
            'type' => ['required', 'string', 'in:annual,monthly']
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $user = User::find(Auth::id());
        if ($user->hasActiveSubscription()) {
            return $this->sendError(['message' => 'This user already Subscriped']);
        }

        // Delete all old Subscriptions that user had before
        Subscription::query()->where('user_id', $user->id)->delete();

        // Price depends on subscription type
        $price = (new Price)->get_subscription_price($request->type);

        // Payment
        $payRequest = new Request(['balance' => $price->price]);
        $response = (new BudgetController)->pay($payRequest);
        if ($response->getData()->status == 'failure') {
            return $response;
        }

        $startsAt = Carbon::now();
        $endsAt = $request->type == 'annual' ? $startsAt->copy()->addYear() : $startsAt->copy()->addMonth();

        // Create a new Subscription
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'price_id' => $price->id,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt
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
