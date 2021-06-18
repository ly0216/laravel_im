<?php

namespace App\Mysql;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GoodsWarehouse extends Model
{
    //
    public $timestamps = false;
    protected $table = 'goods_warehouse';
    const tableName = 'goods_warehouse';

    const CacheKey = 'bp_goods_warehouse_';

    public static function getCacheKey($goods_id)
    {
        return self::CacheKey . $goods_id;
    }

    /**
     * 获取单个
     * @param $goods_warehouse_id
     * @return mixed
     */
    public static function getOne($goods_warehouse_id)
    {
        return Cache::remember(self::getCacheKey($goods_warehouse_id), env('CACHE_TTL', 300), function () use ($goods_warehouse_id) {
            return DB::table(self::tableName)
                ->select('id','name','pic_url','video_url','live_goods','live_goods_status','degree','brand','shop_price','cover_pic','detail')
                ->where('id', $goods_warehouse_id)->first();
        });
    }



}
