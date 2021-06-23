<?php

namespace App\Mysql;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AliLiveGoods extends Model
{
    public $timestamps = false;
    protected $table = 'ali_live_goods';
    const tableName = 'ali_live_goods';

    const CacheKey = 'bp_ali_live_goods_';

    const STATUS_INIT = 0;//待审核
    const STATUS_ON = 1;//审核通过未上架
    const STATUS_OFF = 2;//审核驳回
    const STATUS_UP = 3;//已上架直播间


    public static function getCacheGoodsKey($goods_id)
    {
        return self::CacheKey . 'goods_' . $goods_id;
    }

    /**
     * 验证商品是否上架直播间
     * @param $goods_id
     * @return Model|\Illuminate\Database\Query\Builder|null|object
     */
    public static function getOneByGoods($goods_id)
    {
        return DB::table(self::tableName)
            ->select('id', 'ali_live_id', 'goods_id', 'serial_number')
            ->where('goods_id', $goods_id)
            ->where('goods_status', self::STATUS_UP)
            ->first();

    }
}
