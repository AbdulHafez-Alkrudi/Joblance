<?php

namespace App\Jobs;

use App\Http\Controllers\Payment\PayPalController;
use App\Models\Transaction;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class CheckPayoutStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $transaction_id;
    /**
     * Create a new job instance.
     */
    public function __construct($transaction_id)
    {
        $this->$transaction_id = $transaction_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $provider = new PayPalClient;
            $provider->getAccessToken();

            $transaction = Transaction::find($this->transaction_id);
            $payout_id = $transaction->code;
            $response = $provider->showBatchPayoutDetails($payout_id);

            if (array_key_exists("error", $response)) {
                return;
            }

            if ($response['batch_header']['batch_status'] === 'DENIED' || $response['batch_header']['batch_status'] === 'CANCELED') {
                $temp = (new PayPalController())->cancel_payout($payout_id);
            }

            if ($response['batch_header']['batch_status'] === 'SUCCESS') {
                $temp = (new PayPalController())->success_payout($payout_id);
            }

            CheckPayoutStatus::dispatch($this->transaction_id)->delay(now()->addMinutes(5));
        } catch (Exception $ex) {
            return;
        }
    }
}
