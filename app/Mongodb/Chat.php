<?php

namespace App\Mongodb;

use Jenssegers\Mongodb\Eloquent\Model;

class Chat extends Model
{
    //
    protected $connection = 'mongodb';
    //use SoftDeletes;
    protected $collection = 'txzh_chat';
    protected $primaryKey = '_id';    //设置id

    /** 设置json类型字段 */
    protected $json = [
        'content',
    ];
    /** 类型转换 */
    protected $type = [
        'user_id' => 'integer',
        'content_type' => 'integer',
        'msg_type' => 'integer',
        'time' => 'integer',
    ];

}
