<?php

namespace App\Mongodb;

use Jenssegers\Mongodb\Eloquent\Model;

class PartyMessage extends Model
{
    //
    public $timestamps = false;
    protected $connection = 'mongodb';
    //use SoftDeletes;
    const tableName = 'liy_party_message';
    protected $collection = 'liy_party_message';
    protected $primaryKey = '_id';    //设置id

    protected $fillable = [
        'id',
        'action',
        'party_id',
        'user_id',
        'user_name',
        'user_avatar',
        'content',
        'message_type',
        'content_type',
        'created_at',
        'updated_at'
    ];
    protected $json = [
        'content',
    ];
    protected $type = [
        'id' => 'integer',
        'party_id' => 'integer',
        'user_id' => 'integer',
        'message_type' => 'integer',
        'content_type' => 'integer'
    ];

}
