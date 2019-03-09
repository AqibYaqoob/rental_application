<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;

class ChatMessages extends Model implements AuditableContract
{
    use Auditable;
    protected $primaryKey = 'id';
    protected $table      = 'chat_messages';
    protected $fillable   = ['identifier', 'message', 'from_user_id', 'attachment'];

    public function attachment()
    {
        return $this->hasOne(ChatMessagesAttachment::class, 'id', 'attachment');
    }

    public function user_profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'from_user_id');
    }

    public function user_type()
    {
        return $this->hasOne(User::class, 'id', 'from_user_id')->select('id', 'user_type');
    }
}
