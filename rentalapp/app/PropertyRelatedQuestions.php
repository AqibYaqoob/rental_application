<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;

class PropertyRelatedQuestions extends Model implements AuditableContract
{
    use Auditable;
    protected $table      = 'property_related_questions';
    protected $primaryKey = 'id';
    protected $fillable   = ['property_question_1', 'property_question_2', 'property_question_3', 'property_question_4', 'property_id'];
}
