<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;

class UserProfile extends Model implements AuditableContract
{
    use Auditable;
    protected $primaryKey = 'id';
    protected $fillable   = ['user_id', 'file_name', 'file_path'];
}
