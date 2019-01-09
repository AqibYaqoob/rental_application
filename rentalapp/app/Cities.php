<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;

class Cities extends Model implements AuditableContract
{
    use Auditable;
    protected $primaryKey = 'id';
    protected $fillable   = ['name'];
}
