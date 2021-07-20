<?php

namespace App\Mongodb;

use Jenssegers\Mongodb\Eloquent\Model;

class DaySign extends Model
{
    //
    public $timestamps = false;
    protected $connection = 'mongodb';
    //use SoftDeletes;
    const tableName = 'liy_day_sign';
    protected $collection = 'liy_day_sign';
    protected $primaryKey = '_id';    //设置id

    protected $fillable = [
        'id',
        'user_id',
        'user_name',
        'user_avatar',
        'views',
        'lunar_year',
        'cn_year',
        'cn_month',
        'cn_day',
        'bron_year',
        'week',
        'content',
        'images',
        'created_at',
        'updated_at'
    ];
    protected $json = [
        'images',
    ];
    protected $type = [
        'user_id' => 'integer',
        'views' => 'integer'
    ];

}
