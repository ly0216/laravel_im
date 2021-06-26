<?php

namespace App\Mongodb;

use Jenssegers\Mongodb\Eloquent\Model;

class ChatGroup extends Model
{

    public $timestamps = false;

    protected $connection = 'mongodb';
    //use SoftDeletes;
    protected $collection = 'txzh_chat_group';
    protected $primaryKey = '_id';    //设置id
    protected $fillable = [
        'list_id',
        'label_id',
        'is_open',
        'is_live',
        'main_id',
        'time',
        'name',
        'agent_id',
        'photo_path',
        'notice',
        'is_msg',
        'is_photo',
        'update_time'
    ];
    /** 类型转换 */
    protected $type = [
        'label_id' => 'integer',
        'is_open' => 'integer',
        'is_live' => 'integer',
        'main_id' => 'integer',
        'is_msg' => 'integer',
        'is_photo' => 'integer',
        'time' => 'integer',
        'update_time' => 'integer',
    ];
}
