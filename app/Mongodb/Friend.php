<?php

namespace App\Mongodb;

use Jenssegers\Mongodb\Eloquent\Model;

class Friend extends Model
{
    //
    protected $connection = 'mongodb';
    //use SoftDeletes;
    protected $collection = 'txzh_friend';
    protected $primaryKey = '_id';    //设置id



}
