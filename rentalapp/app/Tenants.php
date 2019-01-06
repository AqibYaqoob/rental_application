<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;

class Tenants extends Model implements AuditableContract
{
    use Auditable;
    protected $table      = 'tenants';
    protected $primaryKey = 'Id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'TenantName',
    ];
    public function user()
    {
        return $this->hasMany('App\User', 'TenantId', 'Id');
    }
}
