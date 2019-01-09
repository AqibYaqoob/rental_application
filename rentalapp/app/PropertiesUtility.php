<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;

class PropertiesUtility extends Model implements AuditableContract
{
    use Auditable;
    protected $table      = 'properties_utility';
    protected $primaryKey = 'id';
    protected $fillable   = ['property_id', 'electric', 'gas', 'water', 'trash'];
}
