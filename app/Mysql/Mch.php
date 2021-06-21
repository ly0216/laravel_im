<?php

namespace App\Mysql;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Mch extends Model
{
    //
    public $timestamps = false;
    protected $table = 'mch';
    const tableName = 'mch';

    const CacheKey = 'bp_mch_';

    public static function getCacheKey($mch_id)
    {
        return self::CacheKey . $mch_id;

    }

    public static function getCacheUserKey($user_id)
    {
        return self::CacheKey . 'user_' . $user_id;
    }

    /**
     * 获取mch
     * @param $mch_id
     * @return mixed
     */
    public static function getOne($mch_id)
    {
        return Cache::remember(self::getCacheKey($mch_id), env('CACHE_TTL', 300), function () use ($mch_id) {
            return DB::table(self::tableName)->where('id', $mch_id)->first();
        });
    }

    /**
     * 根据用户ID获取mch
     * @param $user_id
     * @return mixed
     */
    public static function getUserMch($user_id)
    {
        Cache::forget(self::getCacheUserKey($user_id));
        return Cache::remember(self::getCacheUserKey($user_id), env('CACHE_TTL', 300), function () use ($user_id) {
            return  DB::table(self::tableName)
                ->where('user_id', $user_id)
                ->where('review_status', 1)
                ->where('is_reputation', 1)
                ->first();
        });
    }

}
