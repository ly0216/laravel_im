<?php

namespace App\Mysql;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AliLiveAnchor extends Model
{
    public $timestamps = false;
    protected $table = 'ali_live_anchor';
    const tableName = 'ali_live_anchor';

    const CacheKey = 'bp_ali_live_anchor_';

    public static function getCacheUserKey($user_id)
    {
        return self::CacheKey . 'user_' . $user_id;
    }


    /**
     * 通过用户ID获取主播信息
     * @param $user_id
     * @return mixed
     */
    public static function getAnchorByUser($user_id)
    {
        return Cache::remember(self::getCacheUserKey($user_id), env('CACHE_TTL', 300), function () use ($user_id) {
            return DB::table(self::tableName)->where('user_id', $user_id)
                ->where('is_delete', 0)->where('status', 0)->first();
        });
    }
}
