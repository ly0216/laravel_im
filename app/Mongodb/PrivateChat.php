<?php

namespace App\Mongodb;

use Jenssegers\Mongodb\Eloquent\Model;

class PrivateChat extends Model
{
    //
    public $timestamps = false;
    protected $connection = 'mongodb';
    //use SoftDeletes;
    protected $collection = 'liy_private_chat';
    const tableName = 'liy_private_chat';
    protected $primaryKey = '_id';    //设置id

    protected $fillable = [
        'chat_sn',
        'user_ids_str',
        'is_delete',
        'created_at',
        'updated_at'
    ];
    protected $type = [
        'id' => 'integer',
        'user_id' => 'integer',
        'is_delete' => 'integer'

    ];

}
