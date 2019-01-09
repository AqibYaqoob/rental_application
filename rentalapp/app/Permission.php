<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
   public function roles()
    {
        return $this->belongsToMany(Role::class,'permission_role', 
      'permission_id', 'role_id');
    }
}