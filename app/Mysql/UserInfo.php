<?php

namespace App\Mysql;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UserInfo extends Model
{
    //
    public $timestamps = false;
    protected $table = 'user_info';
    const tableName = 'user_info';

    const CacheKey = 'bp_user_info_';

    public static function getCacheKey($user_id)
    {
        return self::CacheKey . $user_id;

    }

    public static function getOne($user_id)
    {
        return Cache::remember(self::getCacheKey($user_id), env('CACHE_TTL', 300), function () use ($user_id) {
            return DB::table(self::tableName)->where('user_id', $user_id)->first();

        });
    }

}
