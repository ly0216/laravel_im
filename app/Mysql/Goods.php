<?php

namespace App\Mysql;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Goods extends Model
{
    //
    public $timestamps = false;
    protected $table = 'goods';
    const tableName = 'goods';

    const CacheKey = 'bp_goods_';

    public static function getCacheKey($goods_id)
    {
        return self::CacheKey . $goods_id;

    }

    /**
     * 获取单个
     * @param $goods_id
     * @return mixed
     */
    public static function getOne($goods_id)
    {
        return Cache::remember(self::getCacheKey($goods_id), env('CACHE_TTL', 300), function () use ($goods_id) {
           return  DB::table(self::tableName)
                ->select('id','price','goods_warehouse_id','mch_id')
                ->where('id', $goods_id)
                ->first();
        });
    }



}
