<?php

namespace App\Mongodb;

use Jenssegers\Mongodb\Eloquent\Model;

class ChatUnion extends Model
{

    public $timestamps = false;

    protected $connection = 'mongodb';
    //use SoftDeletes;
    protected $collection = 'txzh_chat_union';
    protected $primaryKey = '_id';    //设置id
    protected $fillable = [
        'union_id',
        'list_id',
        'union_sn',
        'title',
        'slogan',
        'notice',
        'leader_user_id',
        'leader_user_name',
        'level',
        'status',
        'share_number',
        'max_online_number',
        'report_number',
        'violation_number',
        'unseal_number',
        'credit_score',
        'max_admin_number',
        'max_online_user',
        'is_delete',
        'deleted_at',
        'created_at',
        'updated_at',
    ];
    /** 类型转换 */
    protected $type = [
        'union_id' => 'integer',
        'leader_user_id' => 'integer',
        'level' => 'integer',
        'status' => 'integer',
        'share_number' => 'integer',
        'max_online_number' => 'integer',
        'report_number' => 'integer',
        'violation_number' => 'integer',
        'unseal_number' => 'integer',
        'credit_score' => 'integer',
        'max_admin_number' => 'integer',
        'max_online_user' => 'integer',
        'is_delete' => 'integer',
    ];
}
