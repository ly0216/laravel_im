<?php

namespace App\Mongodb;

use Jenssegers\Mongodb\Eloquent\Model;

class HeaderImages extends Model
{
    //
    public $timestamps = false;
    protected $connection = 'mongodb';
    //use SoftDeletes;
    const tableName = 'liy_header_images';
    protected $collection = 'liy_header_images';
    protected $primaryKey = '_id';    //设置id

    protected $fillable = [
        'title',
        'apply_sex',//适用性别  1男  2女
        'image_url',
        'used_number',
        'created_at',
        'updated_at'
    ];
    protected $json = [
        'images',
    ];
    protected $type = [
        'used_number' => 'integer',
        'apply_sex' => 'integer'
    ];

}
