<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function get_all_prices($lang)
    {
        return Price::query()->when($lang == 'en' ,
            function($query){
                return $query->select('id','name_EN as name', 'price');
            },
            function($query){
                return $query->select('id','name_AR as name' , 'price');
            }
        )->get();
    }

    public function get_price($id , string $lang , bool $to_array){
        $price = Price::query()->when($lang == 'en' ,
            function($query) use($id){
                return $query->select('id' , 'name_EN as name' , 'price')->where('id' , $id)->first();
            }
            ,
            function($query) use($id){
                return $query->select('id' , 'name_AR as name' , 'price')->where('id' , $id)->first();
            }
        );
        if($to_array) return $price ;
        return $price->price;
    }

    public function get_subscription_price($type)
    {
        return Price::query()->when($type == 'annual',
            function($query) {
                return $query->select('id', 'price')->where('name_EN', 'Annual Subscription');
            },
            function($query) {
                return $query->select('id', 'price')->where('name_EN', 'Monthly Subscription');
            }
        )->first();
    }
}
