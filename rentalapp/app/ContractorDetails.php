<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;

class ContractorDetails extends Model implements AuditableContract
{
    use Auditable;
    protected $table      = 'contractor_details';
    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reference_email', 'reference_phone_number', 'skill_set', 'social_security_number', 'driving_licence', 'user_id',
    ];
}
