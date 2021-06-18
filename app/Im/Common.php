<?php

namespace App\Im;

use App\Common\Code;
use App\Mongodb\MemberFd;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
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
