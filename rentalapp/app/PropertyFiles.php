<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;

class PropertyFiles extends Model implements AuditableContract
{
    use Auditable;
    protected $table      = 'property_files';
    protected $primaryKey = 'id';
    protected $fillable   = ['file_name', 'file_path', 'property_id', 'main_img'];
}
