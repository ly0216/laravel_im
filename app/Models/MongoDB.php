<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MongoDB extends Model
{

    const CacheKey = "mongodb_increment_idx_";

    public static function getCacheKey($table)
    {
        return self::CacheKey . $table;
    }

    public static function getTableIdx($table, $clear = false)
    {
        $cache_key = self::getCacheKey($table);
        if ($clear) {
            Cache::forget($cache_key);
        }
        $idx = 1;
        if (Cache::has($cache_key)) {
            $idx = Cache::increment($cache_key, 1);
        } else {
            $number = DB::connection('mongodb')->table($table)->count();
            Cache::forever($cache_key, $number);
            $idx = Cache::increment($cache_key, 1);
        }
        return $idx;
    }

    public static function getTableAlreadyIdx($table,$idx_name){
        $idx = 1;
        $has  = DB::connection('mongodb')->table($table)->select("{$idx_name}")->orderByDesc("{$idx_name}")->first();
        if($has){
            $idx += $has[$idx_name];
        }
        return $idx;
    }
}
