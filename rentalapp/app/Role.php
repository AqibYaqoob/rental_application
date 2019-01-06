<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;

class Role extends Model implements AuditableContract
{
    protected $fillable = ["name"];
    use Auditable;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role', 'role_id', 'permission_id')->select('screen_id');
    }
}
