<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;

class Suppliers extends Model implements AuditableContract
{
    use Auditable;
    protected $table    = 'suppliers';
    protected $fillable = ["name", "address", "phone_number"];
    // public function sitesAccount()
    // {
    //     return $this->hasMany("App\SitesAccount", "category_id");
    // }
}
