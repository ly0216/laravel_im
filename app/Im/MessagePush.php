<?php

namespace App\Im;

use App\Common\Code;
use App\Mongodb\Friend;
use App\Mongodb\MemberFd;
use App\Mongodb\PartyMember;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class MessagePush extends Model
{



    public static function sendPrivate($chat_sn,$message){
        $list = Friend::where('chat_sn',$chat_sn)->get();
        $swoole = app('swoole');
        foreach ($list as $item => $val) {
            $fd_list = MemberFd::where('user_id', intval($val->user_id))->get();
            foreach ($fd_list as $fItem => $fVal) {
                if ($swoole->isEstablished($fVal->fd_id)) {
                    $swoole->push(intval($fVal->fd_id), json_encode($message));
                } else {
                    MemberFd::where('fd_id', intval($fVal->fd_id))->delete();
                }
            }
        }
    }

    /**
     * 发送消息
     * @param $party_id
     * @param $message
     */
    public static function send($party_id, $message)
    {
        $list = PartyMember::where('party_id', intval($party_id))->where('is_online', 1)->where('is_tick', 0)->get();
        $swoole = app('swoole');
        foreach ($list as $item => $val) {
            $fd_list = MemberFd::where('user_id', intval($val->user_id))->get();
            foreach ($fd_list as $fItem => $fVal) {
                if ($swoole->isEstablished($fVal->fd_id)) {
                    $swoole->push(intval($fVal->fd_id), json_encode($message));
                } else {
                    MemberFd::where('fd_id', intval($fVal->fd_id))->delete();
                }
            }
        }
    }

    /**
     * 单发消息
     * @param $user_id
     * @param $type
     * @param $data
     * @return bool
     */
    public static function sendToOne($user_id, $type, $data)
    {
        if (!$data || !isset($data['list_id'])) {
            return false;
        }
        $pushData = [
            'action' => $type,
            'data' => $data
        ];
        $fd_id = MemberFd::getOneFd($user_id);
        if ($fd_id) {
            $swoole = app('swoole');
            if ($swoole->isEstablished($fd_id)) {
                $swoole->push($fd_id, json_encode($pushData));
            }
        }
        return true;
    }


}
