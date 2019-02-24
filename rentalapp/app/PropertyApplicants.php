<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PropertyApplicants extends Model
{
    protected $table    = 'property_applicants';
    protected $fillable = ['property_id', 'applicant_id', 'status'];

    public function applicant_details()
    {
        return $this->hasOne(User::class, 'id', 'applicant_id');
    }
}
