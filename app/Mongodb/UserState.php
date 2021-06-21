<?php

namespace App\Mongodb;

use Jenssegers\Mongodb\Eloquent\Model;

class UserState extends Model
{
    //
    protected $connection = 'mongodb';
    //use SoftDeletes;
    protected $collection = 'txzh_user_state';
    protected $primaryKey = '_id';    //设置id



}
