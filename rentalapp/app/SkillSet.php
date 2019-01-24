<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;

class SkillSet extends Model implements AuditableContract
{
    use Auditable;
    protected $table      = 'skill_sets_contractor';
    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'skill_name', 'skill_description',
    ];
}
