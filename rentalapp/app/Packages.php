<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;

class Packages extends Model implements AuditableContract
{
    use Auditable;
    protected $table      = 'packages';
    protected $primaryKey = 'id';
    protected $fillable   = ['package_name', 'description', 'properties_range', 'paid_status'];
}
