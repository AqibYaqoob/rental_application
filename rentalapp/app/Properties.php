<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;

class Properties extends Model implements AuditableContract
{
    use Auditable;
    protected $table      = 'properties';
    protected $primaryKey = 'id';
    protected $fillable   = ['description', 'address', 'latitude', 'longitutde', 'zipcode', 'city', 'status'];
}
