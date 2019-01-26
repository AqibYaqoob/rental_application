<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;

class PropertyScheduling extends Model implements AuditableContract
{
    use Auditable;
    protected $table      = 'property_scheduling';
    protected $primaryKey = 'id';
    protected $fillable   = ['property_id', 'applicant_id', 'availibility_date_time', 'status'];

    public function property_detail()
    {
        return $this->hasOne(Properties::class, 'id', 'property_id');
    }

    public function applicant()
    {
        return $this->hasOne(User::class, 'id', 'applicant_id');
    }
}
