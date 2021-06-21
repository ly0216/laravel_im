<?php

namespace App\Mysql;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GoodsBrand extends Model
{
    //
    public $timestamps = false;
    protected $table = 'goods_brand';
    const tableName = 'goods_brand';

    const CacheKey = 'bp_goods_brand_';

    public static function getCacheKey($brand_id)
    {
        return self::CacheKey . $brand_id;
    }

    /**
     * 获取单个
     * @param $brand_id
     * @return mixed
     */
    public static function getOne($brand_id)
    {
        return Cache::remember(self::getCacheKey($brand_id), env('CACHE_TTL', 300), function () use ($brand_id) {
            return DB::table(self::tableName)
                //->select('id','name','pic_url','video_url','live_goods','live_goods_status','degree','brand','shop_price','cover_pic','detail')
                ->where('id', $brand_id)->first();
        });
    }



}
