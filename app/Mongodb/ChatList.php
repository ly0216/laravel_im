<?php

namespace App\Mongodb;

use Jenssegers\Mongodb\Eloquent\Model;

class ChatList extends Model
{
    //
    protected $connection = 'mongodb';
    //use SoftDeletes;
    protected $collection = 'txzh_chat_list';
    protected $primaryKey = '_id';    //设置id


    /** 对话消息类型 */
    public static function chatType($type, $text)
    {
        switch ($type) {
            case 1:
                /** 语音 */
                $last_msg = '[语音]';
                break;
            case 2:
                /** 图片 */
                $last_msg = '[图片]';
                break;
            case 3:
                /** 视频 */
                $last_msg = '[视频]';
                break;
            case 4:
                /** 文件 */
                $last_msg = '[文件]';
                break;
            case 5:
                /** 红包 */
                $last_msg = '[红包]';
                break;
            case 11:
                /** 加入购物车 */
                $last_msg = '[加入购物车]';
                break;
            case 12:
                /** 拍下未付款 */
                $last_msg = '[拍下未付款]';
                break;
            case 13:
                /** 付款成功 */
                $last_msg = '[付款成功]';
                break;
            case 14:
                /** 货到付款 */
                $last_msg = '[货到付款]';
                break;
            case 15:
                /** 已发货 */
                $last_msg = '[已发货]';
                break;
            case 16:
                /** 发起了退款 */
                $last_msg = '[发起了退款]';
                if ($text == '取消成功' || $text == '取消订单') {
                    $last_msg = '[取消订单]';
                }
                break;
            case 17:
                /** 确认收货成功 */
                $last_msg = '[确认收货成功]';
                break;
            case 18:
                /** 已拒绝售后申请 */
                $last_msg = '[已拒绝售后申请]';
                break;
            case 19:
                /** 已同意售后申请 */
                $last_msg = '[已同意售后申请]';
                break;
            case 20:
                /** 退款已完成 */
                $last_msg = '[退款已完成]';
                break;
            case 21:
                /** 进入房间 */
                $last_msg = '[进入房间]';
                break;
            case 22:
                /** 离开房间 */
                $last_msg = '[离开房间]';
                break;
            case 23:
                /** 正在购买 */
                $last_msg = '[正在购买]';
                break;
            case 24:
                /** 购买成功(已付款) */
                $last_msg = '[购买成功(已付款)]';
                break;
            case 25:
                /** 修改商品排序 */
                $last_msg = '[修改商品排序]';
                break;
            case 26:
                /** 商品置顶 */
                $last_msg = '[商品置顶]';
                break;
            case 27:
                /** 上架商品 */
                $last_msg = '[上架商品]';
                break;
            case 28:
                /** 下架商品 */
                $last_msg = '[下架商品]';
                break;
            case 30:
                /** 出售 */
                $last_msg = '[出售]';
                break;
            case 31:
                /** 求购 */
                $last_msg = '[求购]';
                break;
            default:
                /** 未知消息类型 */
                $last_msg = '[未知]';
                break;
        }
        return $last_msg;
    }

    /**
     * 获取已经过了多久
     * PHP时间转换
     * 刚刚、几分钟前、几小时前
     * 今天昨天前天几天前
     * @param  string $targetTime 时间戳
     * @return string
     */
    public static function getLastTime($targetTime)
    {
        // 今天最大时间
        $todayLast = strtotime(date('Y-m-d 23:59:59'));
        $agoTimeTrue = time() - $targetTime;
        $agoTime = $todayLast - $targetTime;
        $agoDay = floor($agoTime / 86400);
        if ($agoTimeTrue < 60) {
            $result = '刚刚';
        } elseif ($agoTimeTrue < 3600) {
            $result = (ceil($agoTimeTrue / 60)) . '分钟前';
        } elseif ($agoTimeTrue < 3600 * 12) {
            $result = (ceil($agoTimeTrue / 3600)) . '小时前';
        } elseif ($agoDay == 0) {
            $result = '今天 ' . date('H:i', $targetTime);
        } elseif ($agoDay == 1) {
            $result = '昨天 ' . date('H:i', $targetTime);
        } elseif ($agoDay == 2) {
            $result = '前天 ' . date('H:i', $targetTime);
        } elseif ($agoDay > 2 && $agoDay < 16) {
            $result = $agoDay . '天前 ' . date('H:i', $targetTime);
        } else {
            $format = date('Y') != date('Y', $targetTime) ? "Y-m-d H:i" : "m-d H:i";
            $result = date($format, $targetTime);
        }
        return $result;
    }
}
