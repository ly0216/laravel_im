<?php

namespace App\Mysql;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Store extends Model
{
    //
    public $timestamps = false;
    protected $table = 'store';
    const tableName = 'store';

    const CacheKey = 'bp_store_';

    public static function getCacheKey($store_id)
    {
        return self::CacheKey . $store_id;
    }

    public static function getCacheMchKey($mch_id)
    {
        return self::CacheKey . 'mch_' . $mch_id;
    }


    public static function getOne($store_id)
    {
        return Cache::remember(self::getCacheKey($store_id), env('CACHE_TTL', 300), function () use ($store_id) {
            return DB::table(self::tableName)
                ->where('id', $store_id)
                ->where('is_delete', 0)
                ->first();
        });
    }

    public static function getOneByMch($mch_id)
    {
        return Cache::remember(self::getCacheMchKey($mch_id), env('CACHE_TTL', 300), function () use ($mch_id) {
            return DB::table(self::tableName)
                ->where('mch_id', $mch_id)
                ->where('is_delete', 0)
                ->first();
        });
    }
}
