<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactionTypes() : BelongsTo
    {
        return $this->belongsTo(TransactionTypes::class);
    }

    public function transactionStatus() : BelongsTo
    {
        return $this->belongsTo(TransactionStatus::class);
    }

    public function get_all_transactions($lang)
    {
        $transactions = $this->all();
        foreach ($transactions as $key => $transaction) {
            $transactions[$key] = $this->get_info($transaction, $lang);
        }
        return $transactions;
    }

    public function get_info($transaction, $lang)
    {
        return [
            'id' => $transaction->id,
            'balance' => $transaction->balance,
            'code' => is_null($transaction->code) ? "" : $transaction->code,
            'transaction_type_id' => (new TransactionTypes)->get_transaction_type($transaction->transactions_type_id, $lang, 0),
            'transaction_status_id' => (new TransactionStatus)->get_transaction_status($transaction->transaction_status_id, $lang, 0),
            'user_id' => $transaction->user_id,
        ];
    }
}
