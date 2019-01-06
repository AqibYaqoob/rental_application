<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;

class RawCategory extends Model implements AuditableContract
{
    use Auditable;
    protected $table    = 'raw_category';
    protected $fillable = ["category_name"];
    // public function sitesAccount()
    // {
    //     return $this->hasMany("App\SitesAccount", "category_id");
    // }
}
