<?php

namespace App\Mongodb;

use Jenssegers\Mongodb\Eloquent\Model;

class PrivateChat extends Model
{
    //
    public $timestamps = false;
    protected $connection = 'mongodb';
    //use SoftDeletes;
    protected $collection = 'liy_private_chat';
    const tableName = 'liy_private_chat';
    protected $primaryKey = '_id';    //设置id

    protected $fillable = [
        'chat_sn',
        'user_id',//发送人的ID
        'user_name',
        'user_avatar',
        'user_ids_str',
        'content',//消息内容
        'message_type',
        'content_type',
        'is_delete',
        'created_at',
        'updated_at'
    ];

    protected $json = [
        'content',
    ];
    protected $type = [
        'user_id' => 'integer',
        'message_type' => 'integer',
        'content_type' => 'integer',
        'is_delete' => 'integer'

    ];

}
