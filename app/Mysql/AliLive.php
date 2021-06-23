<?php

namespace App\Mysql;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AliLive extends Model
{

    public $timestamps = false;
    protected $table = 'ali_live';
    const tableName = 'ali_live';

    const CacheKey = 'bp_ali_live_';

    public static function getCacheKey($live_id)
    {
        return self::CacheKey . 'user_' . $live_id;
    }

    public static function getOne($live_id, $clear = false)
    {
        if ($clear) {
            Cache::forget(self::getCacheKey($live_id));
        }
        return Cache::remember(self::getCacheKey($live_id), env('CACHE_TTL', 300), function () use ($live_id) {
            return DB::table(self::tableName)->where('id', $live_id)->first();
        });
    }
}
