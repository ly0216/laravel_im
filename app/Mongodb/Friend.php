<?php

namespace App\Mongodb;

use Jenssegers\Mongodb\Eloquent\Model;

class Friend extends Model
{
    //
    public $timestamps = false;
    protected $connection = 'mongodb';
    //use SoftDeletes;
    protected $collection = 'txzh_friend';
    protected $primaryKey = '_id';    //设置id

    protected $fillable = [
        'user_id',
        'friend_id',
        'from',
        'remarks',
        'show_circle_he',
        'show_circle_my',
        'time',
        'created_at',
        'updated_at'
    ];
    protected $type = [
        'user_id' => 'integer',
        'friend_id' => 'integer',
        'from' => 'integer',
        'time' => 'integer',
        'created_at' => 'integer',
        'updated_at' => 'integer',
    ];

}
