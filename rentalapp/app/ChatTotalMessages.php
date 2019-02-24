<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatTotalMessages extends Model
{
    protected $primaryKey = 'id';
    protected $table      = 'chat_messages';
    protected $fillable   = ['identifier', 'total_messages'];
}
