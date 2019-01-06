<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;

class Screens extends Model implements AuditableContract
{
    use Auditable;
    protected $table    = 'screens_details';
    protected $fillable = [
        'name', 'url', 'association_id', 'code',
    ];
}
