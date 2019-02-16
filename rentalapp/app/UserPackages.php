<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;

class UserPackages extends Model implements AuditableContract
{
    use Auditable;
    protected $primaryKey = 'id';
    protected $fillable   = ['user_id', 'package_id'];

    public function package_detail()
    {
        return $this->hasOne(Packages::class, 'id', 'package_id');
    }
}
