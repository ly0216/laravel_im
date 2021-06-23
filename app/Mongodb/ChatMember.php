<?php

namespace App\Mongodb;

use Jenssegers\Mongodb\Eloquent\Model;

class ChatMember extends Model
{
    //
    public $timestamps = false;
    protected $connection = 'mongodb';
    //use SoftDeletes;
    protected $collection = 'txzh_chat_member';
    protected $primaryKey = '_id';    //设置id
    protected $fillable = [
        'list_id',
        'user_id',
        'nickname',
        'is_admin',
        'is_msg',
        'is_onLine',
        'time',
        'created_at',
        'updated_at'
    ];
    protected $type = [
        'user_id' => 'integer',
        'created_at' => 'integer',
        'updated_at' => 'integer',
    ];

}
