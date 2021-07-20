<?php

namespace App\Mongodb;

use Jenssegers\Mongodb\Eloquent\Model;

class PartyList extends Model
{
    //
    public $timestamps = false;
    protected $connection = 'mongodb';
    //use SoftDeletes;
    const tableName = 'liy_party_list';
    protected $collection = 'liy_party_list';
    protected $primaryKey = '_id';    //设置id

    protected $fillable = [
        'id',
        'chat_sn',
        'user_id',
        'user_name',
        'user_avatar',
        'title',
        'content',
        'online_number',
        'views',
        'is_delete',
        'created_at',
        'updated_at'
    ];

    protected $type = [
        'id' => 'integer',
        'user_id' => 'integer',
        'views' => 'integer',
        'is_delete' => 'integer'
    ];

}
