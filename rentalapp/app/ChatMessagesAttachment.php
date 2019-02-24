<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;

class ChatMessagesAttachment extends Model implements AuditableContract
{
    use Auditable;
    protected $primaryKey = 'id';
    public $timestamps    = false;
    protected $table      = 'chat_message_attachments';
    protected $fillable   = ['chat_messages_identifiers', 'attachment_file_path', 'attachment_file_name'];
}
