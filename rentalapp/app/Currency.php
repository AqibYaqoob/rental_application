<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;

class Currency extends Model implements AuditableContract
{
    use Auditable;
    use SoftDeletes;
    protected $primaryKey = 'Id';
    protected $fillable   = ['CurrencyName', 'Tenant_Id', 'isBaseCurrency', 'deleted_at', 'CurrentRate'];
    protected $dates      = ['deleted_at'];
    public function currencyList()
    {
        return $this->belongsTo('App\CurrencyList', 'currency_id', 'id');
    }
    public function partnerAccountDetails()
    {
        return $this->hasMany("App\PartnerAccountDetails", "currencyId", "Id");
    }
    public function sitesAccount()
    {
        return $this->hasMany("App\SitesAccount", "CurrencyId", "Id");
    }
    public function tenantAccountDetails()
    {
        return $this->hasMany("App\TenantAccountDetails", "currencyId", "Id");
    }
}
