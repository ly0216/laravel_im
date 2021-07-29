<?php

namespace App\Mongodb;

use Jenssegers\Mongodb\Eloquent\Model;

class Friend extends Model
{
    //
    public $timestamps = false;
    protected $connection = 'mongodb';
    //use SoftDeletes;
    protected $collection = 'liy_friend';
    const tableName  = 'liy_friend';
    protected $primaryKey = '_id';    //设置id

    protected $fillable = [
        'id',
        'user_id',
        'user_ids_str',
        'friend_id',
        'remarks',
        'created_at',
        'updated_at'
    ];
    protected $type = [
        'id' => 'integer',
        'user_id' => 'integer',
        'friend_id' => 'integer'

    ];

}
