<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;

class ChatTotalMessages extends Model implements AuditableContract
{
    use Auditable;
    protected $primaryKey = 'id';
    protected $table      = 'chat_messages';
    protected $fillable   = ['identifier', 'total_messages'];
}
