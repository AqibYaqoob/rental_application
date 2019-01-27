<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;

class PropertyAddFavourite extends Model implements AuditableContract
{
    use Auditable;
    protected $table      = 'property_add_to_favourite';
    protected $primaryKey = 'id';
    protected $fillable   = ['property_id', 'applicant_id'];

    public function properties_detail()
    {
        return $this->hasOne(Properties::class, 'id', 'property_id');
    }
}
