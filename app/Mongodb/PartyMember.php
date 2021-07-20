<?php

namespace App\Mongodb;

use Jenssegers\Mongodb\Eloquent\Model;

class PartyMember extends Model
{
    //
    public $timestamps = false;
    protected $connection = 'mongodb';
    //use SoftDeletes;
    const tableName = 'liy_party_member';
    protected $collection = 'liy_party_member';
    protected $primaryKey = '_id';    //设置id

    protected $fillable = [
        'id',
        'party_id',
        'user_id',
        'user_name',
        'user_avatar',
        'is_online',
        'is_tick',
        'created_at',
        'updated_at'
    ];

    protected $type = [
        'id' => 'integer',
        'user_id' => 'integer',
        'is_online' => 'integer',
        'is_tick' => 'integer'
    ];

}
