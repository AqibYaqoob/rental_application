<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;

class PropertyType extends Model implements AuditableContract
{
    use Auditable;
    protected $table      = 'property_type';
    protected $primaryKey = 'id';
    protected $fillable   = ['name'];
}
