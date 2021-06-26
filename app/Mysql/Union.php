<?php

namespace App\Mysql;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Union extends Model
{
    //
    public $timestamps = false;
    protected $table = 'union';
    const tableName = 'union';
    const CacheKey = 'bp_union_';
    const DELETE_ON = 1;
    const DELETE_OFF = 0;

    public static function getCacheKey($union_id)
    {
        return self::CacheKey . 'union_' . $union_id;
    }

    public static function getCacheSnKey($union_sn)
    {
        return self::CacheKey . 'union_sn_' . $union_sn;
    }

    /**
     * 根据ID获取
     * @param $union_id
     * @param bool $clear
     * @return mixed
     */
    public static function getOne($union_id,$clear = false)
    {
        if($clear){
            Cache::forget(self::getCacheKey($union_id));
        }
        return Cache::remember(self::getCacheKey($union_id), env('CACHE_TTL', 300), function () use ($union_id) {
            return DB::table(self::tableName)->where('id', $union_id)
                ->where('is_delete', self::DELETE_OFF)->first();
        });
    }

    /**
     * 根据SN获取
     * @param $union_sn
     * @param bool $clear
     * @return mixed
     */
    public static function getBySnOne($union_sn,$clear = false)
    {
        if($clear){
            Cache::forget(self::getCacheSnKey($union_sn));
        }
        return Cache::remember(self::getCacheSnKey($union_sn), env('CACHE_TTL', 300), function () use ($union_sn) {
            return DB::table(self::tableName)->where('union_sn', $union_sn)
                ->where('is_delete', self::DELETE_OFF)->first();
        });
    }

    /**
     * 生成联盟SN
     * @param string $prefix
     * @return string
     */
    public static function generateSn($prefix = '')
    {
        $px = '';
        if ($prefix) {
            $px = strtoupper($prefix);
        } else {
            $px = strtoupper(env("UNION_PREFIX", "BP"));
        }

        $start = 0;
        $end = 9;
        $length = 8;
        //初始化变量为0
        $count = 0;
        //建一个新数组
        $temp = [];
        while ($count < $length) {
            //在一定范围内随机生成一个数放入数组中
            $temp[] = mt_rand($start, $end);
            //$data = array_unique($temp);
            //去除数组中的重复值用了“翻翻法”，就是用array_flip()把数组的key和value交换两次。这种做法比用 array_unique() 快得多。
            $data = array_flip(array_flip($temp));
            //将数组的数量存入变量count中
            $count = count($data);
        }
        //为数组赋予新的键名
        shuffle($data);
        //数组转字符串
        $str = implode(",", $data);
        //替换掉逗号
        $suffix = str_replace(',', '', $str);
        $sn = $px . $suffix;
        return $sn;
    }


}
