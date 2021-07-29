<?php

namespace App\Mongodb;

use Jenssegers\Mongodb\Eloquent\Model;

class Collection extends Model
{
    //
    public $timestamps = false;
    protected $connection = 'mongodb';
    //use SoftDeletes;
    protected $collection = 'liy_collection';
    const tableName  = 'liy_collection';
    protected $primaryKey = '_id';    //设置id

    protected $fillable = [
        'id',
        'user_id',
        'party_id',
        'is_top',
        'is_delete',
        'created_at',
        'updated_at'
    ];
    protected $type = [
        'id' => 'integer',
        'user_id' => 'integer',
        'is_top' => 'integer',
        'is_delete' => 'integer'

    ];

}
