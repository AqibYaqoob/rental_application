<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;

class PropertyRelatedAnswers extends Model implements AuditableContract
{
    use Auditable;
    protected $table      = 'property_related_answers';
    protected $primaryKey = 'id';
    protected $fillable   = ['answer_1', 'answer_2', 'answer_3', 'answer_4', 'property_id', 'applicant_id'];
}
