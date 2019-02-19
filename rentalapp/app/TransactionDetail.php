<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;

class TransactionDetail extends Model implements AuditableContract
{
    use Auditable;
    protected $table      = 'transaction_details';
    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transaction_type', 'amount', 'payment_option', 'user_id',
    ];

    public function payment_option()
    {
        return $this->hasOne(PaymentOptions::class, 'id', 'payment_option');
    }
}
