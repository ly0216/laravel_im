<?php

namespace App\Mongodb;

use Jenssegers\Mongodb\Eloquent\Model;

class FriendApply extends Model
{
    //
    protected $connection = 'mongodb';
    //use SoftDeletes;
    protected $collection = 'liy_friend_apply';
    const tableName = 'liy_friend_apply';
    protected $primaryKey = '_id';    //设置id

    protected $fillable = [
        'user_id',
        'user_ids_str',
        'friend_id',
        'friend_nickname',
        'friend_avatar',
        'status',//2 未处理   1通过
        'created_at',
        'updated_at'
    ];
    protected $type = [
        'id' => 'integer',
        'user_id' => 'integer',
        'friend_id' => 'integer',
        'status' => 'integer'

    ];

}
