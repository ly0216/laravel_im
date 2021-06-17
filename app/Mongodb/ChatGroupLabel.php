<?php

namespace App\Mongodb;

use Jenssegers\Mongodb\Eloquent\Model;

class ChatGroupLabel extends Model
{
    //
    protected $connection = 'mongodb';
    //use SoftDeletes;
    protected $collection = 'txzh_chat_group_label';    //文档名
    protected $primaryKey = '_id';    //设置id
    protected $guarded = ['label_id', 'name', 'is_delete'];  //设置字段白名单
    protected $dates = ['deleted_at'];

}
