<?php

namespace App\Im;

use App\Common\Code;
use App\Mongodb\ChatMember;
use App\Mongodb\MemberFd;
use Illuminate\Database\Eloquent\Model;
use Swoole\Exception;

class Common extends Model
{
    /*
     * IM 消息公共处理类
     */

    /**
     * 接收所有消息-验证并处理后续逻辑
     * @param $fd
     * @param $receive_data
     * @return false|string
     */
    public static function checkData($fd, $receive_data)
    {
        try {
            $return_data = [];
            if (!$fd || !$receive_data) {
                return json_encode(['code' => Code::HTTP_ERROR, 'message' => '缺少参数']);
            }
            if (!self::is_json($receive_data)) {
                return json_encode(['code' => Code::HTTP_ERROR, 'message' => '报文数据格式错误']);
            }
            $data = json_decode($receive_data, true);
            if (!isset($data['action'])) {
                return json_encode(['code' => Code::HTTP_ERROR, 'message' => '无效的用户动作']);
            }
            $action = $data['action'];
            if ($action == 'checkToken') {
                //socketId 与userId 绑定
                if (!isset($data['token'])) {
                    return json_encode(['code' => Code::HTTP_ERROR, 'message' => '缺少Token信息']);
                }
                if (!isset($data['user_id'])) {
                    return json_encode(['code' => Code::HTTP_ERROR, 'message' => '缺少用户信息']);
                }
                $token = $data['token'];
                $user_id = $data['user_id'];
                if (!$user_id) {
                    return json_encode(['code' => Code::HTTP_ERROR, 'message' => '无效的用户信息']);
                }
                $memberFd = new MemberFd();
                $res = $memberFd->setMemberFd($user_id, $fd);
                if (!$res) {
                    return json_encode(['code' => Code::HTTP_ERROR, 'message' => '用户绑定失败']);
                }
                $return_data['fd'] = $fd;
            }

            return json_encode(['code' => Code::HTTP_SUCCESS, 'message' => 'ok', 'data' => $return_data]);
        } catch (Exception $exception) {
            return json_encode(['code' => Code::HTTP_ERROR, 'message' => $exception->getMessage()]);
        }

    }


    public static function messageType($type , $nickname , $content)
    {
        $title = $nickname;

        switch ($type) {
            case 0:
                /** 消息 */
                $title = $nickname;
                $body = $content;
                break;
            case 1:
                /** 语音 */
                $body = '[语音]';
                break;
            case 2:
                /** 图片 */
                $body = '[图片]';
                break;
            case 3:
                /** 视频 */
                $body = '[视频]';
                break;
            case 4:
                /** 文件 */
                $body = '[文件]';
                break;
            case 5:
                /** 红包 */
                $body = '[红包]';
                break;
            case 11:
                /** 加入购物车 */
                $body = '[加入购物车]';
                break;
            case 12:
                /** 拍下未付款 */
                $body = '[拍下未付款]';
                break;
            case 13:
                /** 付款成功 */
                $body = '[付款成功]';
                break;
            case 14:
                /** 货到付款 */
                $body = '[货到付款]';
                break;
            case 15:
                /** 已发货 */
                $body = '[已发货]';
                break;
            case 16:
                /** 发起了退款 */
                $body = '[发起了退款]';
                break;
            case 17:
                /** 确认收货成功 */
                $body = '[确认收货成功]';
                break;
            case 18:
                /** 已拒绝售后申请 */
                $body = '[已拒绝售后申请]';
                break;
            case 19:
                /** 已同意售后申请 */
                $body = '[已同意售后申请]';
                break;
            case 20:
                /** 退款已完成 */
                $body = '[退款已完成]';
                break;
            case 21:
                /** 进入房间 */
                $body = '[进入房间]';
                break;
            case 22:
                /** 离开房间 */
                $body = '[离开房间]';
                break;
            case 23:
                /** 正在购买 */
                $body = '[正在购买]';
                break;
            case 24:
                /** 购买成功(已付款) */
                $body = '[购买成功(已付款)]';
                break;
            case 25:
                /** 修改商品排序 */
                $body = '[修改商品排序]';
                break;
            case 26:
                /** 商品置顶 */
                $body = '[商品置顶]';
                break;
            case 27:
                /** 上架商品 */
                $body = '[上架商品]';
                break;
            case 28:
                /** 下架商品 */
                $body = '[下架商品]';
                break;
            case 30:
                /** 出售 */
                $body = '[出售]';
                break;
            case 31:
                /** 求购 */
                $body = '[求购]';
                break;
            default:
                /** 未知消息类型 */
                $body = '[未知]';
                break;
        }
        return $data = [
            'title' => $title,
            'body' => $body
        ];
    }

    /**
     * 校验是否为正确的json格式
     * @param $json
     * @return bool
     */
    static function is_json($json)
    {
        json_decode($json);
        return (json_last_error() === JSON_ERROR_NONE);
    }
}
