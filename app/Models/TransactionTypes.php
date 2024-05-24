<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransactionTypes extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_EN', 'name_AR'
    ];

    public function transactions() : HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function get_all_transaction_types(string $lang): Collection|array
    {
        return TransactionTypes::query()->when($lang == 'en' ,
            function($query){
                return $query->select('id','name_EN as name');
            },
            function($query){
                return $query->select('id','name_AR as name');
            }
        )->get();
    }

    public function get_transaction_type(string $name, string $lang , bool $to_array)
    {
        $transaction_type = TransactionTypes::query()->when($lang == 'en' ,
            function($query) use($name){
                return $query->select('id' , 'name_EN as name')->where('name_EN' , $name)->first();
            }
            ,
            function($query) use($name){
                return $query->select('id' , 'name_AR as name')->where('name_AR', $name)->first();
            }
        );
        if($to_array) return $transaction_type ;
        return $transaction_type->name;
    }
}
