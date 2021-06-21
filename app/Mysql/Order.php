<?php

namespace App\Mysql;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Order extends Model
{
    //
    public $timestamps = false;
    protected $table = 'order';
    const tableName = 'order';

    const CacheKey = 'bp_order_';

    public static function getCacheKey($order_id)
    {
        return self::CacheKey . $order_id;

    }

    public static function getCacheSnKey($order_sn)
    {
        return self::CacheKey . 'order_sn_' . $order_sn;
    }

    /**
     * 获取单个
     * @param $order_id
     * @return mixed
     */
    public static function getOne($order_id)
    {
        return Cache::remember(self::getCacheKey($order_id), env('CACHE_TTL', 300), function () use ($order_id) {
            return DB::table(self::tableName)
                ->where('id', $order_id)
                ->first();
        });
    }

    public static function getSnAll($order_sn)
    {
        return Cache::remember(self::getCacheKey($order_sn), env('CACHE_TTL', 300), function () use ($order_sn) {
            return DB::table(self::tableName)
                ->where('order_no', $order_sn)
                ->get();
        });
    }

}
