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



}
