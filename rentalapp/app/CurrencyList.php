<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CurrencyList extends Model
{
    protected $table = "currencies_list";
    protected $fillable = ["country", "currency", "code", "symbol"];
    public function currency()
    {
        return $this->hasOne('App\Currency', 'currency_id');
    }
}
