<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;

class TenantSettings extends Model implements AuditableContract
{
    use Auditable;
    protected $table    = "tenant_setting";
    protected $fillable = ['SettingName', 'ValueData', 'TenantId', 'UserId'];
}
