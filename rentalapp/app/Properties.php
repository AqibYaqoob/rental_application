<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;

class Properties extends Model implements AuditableContract
{
    use Auditable;
    protected $table      = 'properties';
    protected $primaryKey = 'id';
    protected $fillable   = ['description', 'address', 'latitude', 'longitutde', 'zipcode', 'city', 'status', 'user_id', 'property_type'];

    public function properties_utility()
    {
        return $this->hasOne(PropertiesUtility::class, 'property_id', 'id');
    }

    public function properties_files()
    {
        return $this->hasMany(PropertyFiles::class, 'property_id', 'id');
    }

    public function properties_schedulings()
    {
        return $this->hasMany(PropertyScheduling::class, 'property_id', 'id');
    }

    public function property_type()
    {
        return $this->hasOne(PropertyType::class, 'id', 'property_type');
    }

    public function city_detail()
    {
        return $this->hasOne(Cities::class, 'id', 'city');
    }
}
