<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;

class StaffMember extends Model implements AuditableContract
{
    use Auditable;
    protected $table    = 'staff_contact_details';
    protected $fillable = [
        'mobile_number', 'home_number', 'user_id', 'role_id', 'staff_name',
    ];

    public function user_roles()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }
}
