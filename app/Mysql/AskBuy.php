<?php

namespace App\Mysql;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AskBuy extends Model
{
    //
    public $timestamps = false;
    protected $table = 'ask_buy';
    const tableName = 'ask_buy';

    const CacheKey = 'bp_ask_buy_';

    public static function getCacheKey($ask_id)
    {
        return self::CacheKey . $ask_id;

    }

    public static function getCacheGoodsKey($goods_id){
        return self::CacheKey.'goods_'.$goods_id;
    }

    /**
     * 获取单个
     * @param $goods_id
     * @return mixed
     */
    public static function getOne($goods_id)
    {
        return Cache::remember(self::getCacheGoodsKey($goods_id), env('CACHE_TTL', 300), function () use ($goods_id) {
            return  DB::table(self::tableName)
                ->select('id','cover_pic')
                ->where('id', $goods_id)
                ->first();
        });
    }



}
