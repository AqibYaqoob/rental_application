<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;

class RawItems extends Model implements AuditableContract
{
    use Auditable;
    protected $table    = 'raw_items';
    protected $fillable = ["product_name", "category"];
    public function raw_category()
    {
        return $this->hasOne("App\RawCategory", "id", "category");
    }
}
