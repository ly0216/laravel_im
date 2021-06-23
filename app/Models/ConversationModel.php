<?php

namespace App\Models;

use App\Im\MessagePush;
use App\Mongodb\Chat;
use App\Mongodb\ChatList;
use App\Mongodb\ChatMember;
use App\Mongodb\Friend;
use Illuminate\Database\Eloquent\Model;

class ConversationModel extends Model
{
    //消息发起人ID
    public $send_user_id;
    public $to_user_id;
    public $goods_type;
    public $goods_id;
    public $mch_id;

    /**
     * 创建客服会话
     * @return bool|string|array
     */
    public function customerService()
    {
        try {
            $at = time();
            //1.获取客服配置-客服的用户ID
            $cs_user_id = env('CUSTOMER_SERVICE_USER', 9937);
            //2.检测会话是否存在
            $user_list = [intval($this->send_user_id), intval($cs_user_id)];
            sort($user_list);
            $user_list_json = json_encode($user_list);
            $has = ChatList::select('list_id')->where('user_ids', $user_list_json)->where('user_id', intval($this->send_user_id))->first();
            if ($has) {
                return $has->list_id;
            }
            $list_id = md5(uniqid('JWT', true) . rand(1, 100000));
            foreach ($user_list as $item => $val) {
                $no_reader_num = 0;
                if ($val == $cs_user_id) {
                    $no_reader_num = 1;
                }
                $is_online = 0;
                if ($val == $this->send_user_id) {
                    $is_online = 1;
                }
                //3.创建会话列表
                ChatList::create([
                    'user_id' => $val,
                    'list_id' => $list_id,
                    'user_ids' => $user_list_json,
                    'status' => 0,
                    'type' => 0,
                    'goods_id' => 0,
                    'top' => 1,
                    'top_time' => 0,
                    'no_reader_num' => $no_reader_num,
                    'ignore' => 0,
                    'temporary' => 0,
                    'past_time' => 0,
                    'created_at' => $at,
                    'updated_at' => $at
                ]);

                //4.添加会话成员
                ChatMember::create([
                    'list_id' => $list_id,
                    'user_id' => $val,
                    'nickname' => '',
                    'is_admin' => 0,
                    'is_msg' => 0,
                    'is_onLine' => $is_online,
                    'time' => $at,
                    'created_at' => $at,
                    'updated_at' => $at
                ]);
                //5.添加好友
                $friend_id = ($cs_user_id == $val) ? $this->send_user_id : $cs_user_id;
                Friend::create([
                    'user_id' => intval($val),
                    'friend_id' => intval($friend_id),
                    'from' => 4,
                    'remarks' => '',
                    'show_circle_he' => 0,
                    'show_circle_my' => 0,
                    'time' => $at,
                    'created_at' => $at,
                    'updated_at' => $at
                ]);
            }
            //6.发送欢迎语--消息入库
            $content = [
                'list_id' => $list_id,
                'user_id' => intval($cs_user_id),
                'content_type' => 0,
                'msg_type' => 0,
                'content' => [
                    'text' => '欢迎来到包铺二手奢侈品交易中心!',
                ],
                'time' => $at,
            ];
            Chat::create($content);
            //7.发送欢迎消息
            MessagePush::pushMessage($cs_user_id, $list_id, '', 0, $content);

            return $list_id;
        } catch (\Exception $exception) {
            return false;
        }
    }


    /**
     * 创建私聊会话
     * @return bool|string
     */
    public function privateChat()
    {
        try {
            $at = time();
            $user_list = [intval($this->send_user_id), intval($this->to_user_id)];
            sort($user_list);
            $user_list_json = json_encode($user_list);
            //验证是否是好友
            $is_friend = Friend::select('user_id','friend_id')
                ->where('user_id',intval($this->send_user_id))
                ->where('friend_id',intval($this->to_user_id))->first();
            if(!$is_friend){
                return 'no';
            }
            $has = ChatList::select('list_id')->where('user_ids', $user_list_json)->where('user_id', intval($this->send_user_id))->first();
            if ($has) {
                return $has->list_id;
            }
            $list_id = md5(uniqid('JWT', true) . rand(1, 100000));
            foreach ($user_list as $item => $val) {
                $no_reader_num = 0;
                if ($val == $this->to_user_id) {
                    $no_reader_num = 1;
                }
                $is_online = 1;
                if ($val == $this->send_user_id) {
                    $is_online = 1;
                }
                //3.创建会话列表
                ChatList::create([
                    'user_id' => $val,
                    'list_id' => $list_id,
                    'user_ids' => $user_list_json,
                    'status' => 0,
                    'type' => 0,
                    'goods_id' => 0,
                    'top' => 1,
                    'top_time' => 0,
                    'no_reader_num' => $no_reader_num,
                    'ignore' => 0,
                    'temporary' => 0,
                    'past_time' => 0,
                    'created_at' => $at,
                    'updated_at' => $at
                ]);

                //4.添加会话成员
                ChatMember::create([
                    'list_id' => $list_id,
                    'user_id' => $val,
                    'nickname' => '',
                    'is_admin' => 0,
                    'is_msg' => 0,
                    'is_onLine' => $is_online,
                    'time' => $at,
                    'created_at' => $at,
                    'updated_at' => $at
                ]);

            }
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }


    public function goodsChat(){
        try {
            $at = time();
            $user_list = [intval($this->send_user_id), intval($this->to_user_id)];
            sort($user_list);
            $user_list_json = json_encode($user_list);
            //验证是否是好友
            $is_friend = Friend::select('user_id','friend_id')
                ->where('user_id',intval($this->send_user_id))
                ->where('friend_id',intval($this->to_user_id))->first();
            if(!$is_friend){
                return 'no';
            }
            $has = ChatList::select('list_id')->where('user_ids', $user_list_json)->where('user_id', intval($this->send_user_id))->first();
            if ($has) {
                return $has->list_id;
            }
            $list_id = md5(uniqid('JWT', true) . rand(1, 100000));
            foreach ($user_list as $item => $val) {
                $no_reader_num = 0;
                if ($val == $this->to_user_id) {
                    $no_reader_num = 1;
                }
                $is_online = 1;
                if ($val == $this->send_user_id) {
                    $is_online = 1;
                }
                //3.创建会话列表
                ChatList::create([
                    'user_id' => $val,
                    'list_id' => $list_id,
                    'user_ids' => $user_list_json,
                    'status' => 0,
                    'type' => 0,
                    'goods_id' => 0,
                    'top' => 1,
                    'top_time' => 0,
                    'no_reader_num' => $no_reader_num,
                    'ignore' => 0,
                    'temporary' => 0,
                    'past_time' => 0,
                    'created_at' => $at,
                    'updated_at' => $at
                ]);

                //4.添加会话成员
                ChatMember::create([
                    'list_id' => $list_id,
                    'user_id' => $val,
                    'nickname' => '',
                    'is_admin' => 0,
                    'is_msg' => 0,
                    'is_onLine' => $is_online,
                    'time' => $at,
                    'created_at' => $at,
                    'updated_at' => $at
                ]);

            }
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }
}
