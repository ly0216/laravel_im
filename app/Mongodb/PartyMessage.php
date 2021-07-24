<?php

namespace App\Mongodb;

use Jenssegers\Mongodb\Eloquent\Model;

class PartyMessage extends Model
{
    //
    public $timestamps = false;
    protected $connection = 'mongodb';
    //use SoftDeletes;
    const tableName = 'liy_party_message';
    protected $collection = 'liy_party_message';
    protected $primaryKey = '_id';    //设置id

    const MESSAGE_TYPE_USER = 0;//用户普通消息
    const MESSAGE_TYPE_SYS  = 1;//系统消息

    const CONTENT_TYPE_TEXT = 0;//文字消息
    const CONTENT_TYPE_IMAGE = 1;//图片消息

    protected $fillable = [
        'id',
        'action',
        'party_id',
        'user_id',
        'user_name',
        'user_avatar',
        'content',
        'message_type',
        'content_type',
        'created_at',
        'updated_at'
    ];
    protected $json = [
        'content',
    ];
    protected $type = [
        'id' => 'integer',
        'party_id' => 'integer',
        'user_id' => 'integer',
        'message_type' => 'integer',
        'content_type' => 'integer'
    ];

}
