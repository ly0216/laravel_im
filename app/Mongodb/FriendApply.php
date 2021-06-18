<?php

namespace App\Mongodb;

use Jenssegers\Mongodb\Eloquent\Model;

class FriendApply extends Model
{
    //
    protected $connection = 'mongodb';
    //use SoftDeletes;
    protected $collection = 'txzh_friend_apply';
    protected $primaryKey = '_id';    //设置id



}
