<?php

namespace App\Models;

use App\Im\MessagePush;
use App\Mongodb\Chat;
use App\Mongodb\ChatGroup;
use App\Mongodb\ChatList;
use App\Mongodb\ChatMember;
use App\Mongodb\ChatUnion;
use App\Mongodb\Friend;
use App\Mysql\Goods;
use App\Mysql\Mch;
use App\Mysql\Union;
use App\Mysql\UserInfo;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConversationModel extends Model
{
    //消息发起人ID
    public $send_user_id;
    public $to_user_id;
    public $goods_type;
    public $goods_id;
    public $mch_id;
    public $live_name = '';
    public $live_notice = '';
    public $live_label = 0;
    public $photo_path = '';
    public $union_id = 0;
    public $content_type;
    public $content;

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
            $list_id = self::getListId();
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
                $res = ChatList::create([
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
                if (!$res) {
                    return false;
                }

                //4.添加会话成员
                $resM = ChatMember::create([
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
                if (!$resM) {
                    return false;
                }
                //5.添加好友
                $friend_id = ($cs_user_id == $val) ? $this->send_user_id : $cs_user_id;
                $resF = Friend::create([
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
                if (!$resF) {
                    return false;
                }
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
            $is_friend = Friend::select('user_id', 'friend_id')
                ->where('user_id', intval($this->send_user_id))
                ->where('friend_id', intval($this->to_user_id))->first();
            if (!$is_friend) {
                return 'no';
            }
            $has = ChatList::select('list_id')->where('user_ids', $user_list_json)->where('user_id', intval($this->send_user_id))->first();
            if ($has) {
                return $has->list_id;
            }
            $list_id = self::getListId();
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
                $res = ChatList::create([
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
                if (!$res) {
                    return false;
                }
                //4.添加会话成员
                $resM = ChatMember::create([
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
                if (!$resM) {
                    return false;
                }

            }
            return $list_id;
        } catch (\Exception $exception) {
            return false;
        }
    }


    /**
     * 创建商品会话
     * @return bool|string
     */
    public function goodsChat()
    {
        try {
            $at = time();
            $to_user_id = $this->to_user_id;
            if ($this->mch_id) {//有mchId则查询商户绑定的用户
                $mch = Mch::getOne($this->mch_id);
                if ($mch) {
                    $to_user_id = $mch->user_id;
                }
            }
            $user_list = [intval($this->send_user_id), intval($to_user_id)];
            sort($user_list);
            $user_list_json = json_encode($user_list);
            //验证是否存在会话
            $has = ChatList::select('list_id')
                ->where('type', intval($this->goods_type))
                ->where('user_ids', $user_list_json)
                ->where('goods_id', intval($this->goods_id))->first();
            if ($has) {

                return $has->list_id;
            }
            Log::channel('push-message')->info('会话不存在,走创建流程');
            $hasGoods = Goods::getOne($this->goods_id);
            if (!$hasGoods) {

                return false;
            }
            $content_type = MessagePush::CONTENT_GOODS_WANTO;
            $text = '我想要这个商品';
            if ($this->goods_type == 5) {
                $content_type = MessagePush::CONTENT_GOODS_HAVE;
                $text = '我有这个商品';
            }
            $list_id = self::getListId();

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
                $res = ChatList::create([
                    'user_id' => $val,
                    'list_id' => $list_id,
                    'user_ids' => $user_list_json,
                    'status' => 0,
                    'type' => intval($this->goods_type),
                    'goods_id' => intval($this->goods_id),
                    'top' => 1,
                    'top_time' => 0,
                    'no_reader_num' => $no_reader_num,
                    'ignore' => 0,
                    'temporary' => 0,
                    'past_time' => 0,
                    'created_at' => $at,
                    'updated_at' => $at
                ]);

                if (!$res) {
                    return false;
                }
                //4.添加会话成员
                $resM = ChatMember::create([
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

                if (!$resM) {
                    return false;
                }
            }
            /** 创建系统默认消息 */
            $chatRow = Chat::create([
                'list_id' => $list_id,
                'user_id' => $this->send_user_id,
                'content_type' => $content_type,
                'msg_type' => MessagePush::MESSAGE_SYSTEM, //系统消息
                'content' => [
                    'text' => $text,
                    'goods_id' => intval($this->goods_id),
                ],
                'time' => $at,
            ]);
            $user = User::getOne($this->send_user_id);
            $userInfo = UserInfo::getOne($this->send_user_id);
            $content = [
                'list_id' => $list_id,
                'data' => [
                    'type' => MessagePush::MESSAGE_USER,
                    'msg' => [
                        'id' => $chatRow->_id,
                        'type' => $content_type,
                        'time' => $at,
                        'user_info' => [
                            'uid' => intval($this->send_user_id),
                            'name' => isset($user->nickname) ? $user->nickname : '',
                            'face' => isset($userInfo->avatar) ? $userInfo->avatar : ''
                        ],
                        'content' => ['text' => $text, 'goods_id' => intval($this->goods_id)],
                    ],
                ]
            ];
            /**
             * 发送通知
             */
            MessagePush::sendToOne($this->to_user_id, 'chatData', $content);

            return $list_id;
        } catch (\Exception $exception) {
            return false;
        }
    }


    /**
     * 创建直播会话
     * @return bool|string
     */
    public function liveChat()
    {
        try {
            $at = time();
            $user_list = [intval($this->send_user_id)];
            $user_list_json = json_encode($user_list);
            $list_id = self::getListId();
            //1.创建直播群聊
            $res = ChatGroup::create([
                'list_id' => $list_id,
                'label_id' => $this->live_label,
                'is_open' => 0,
                'is_live' => 1,
                'main_id' => intval($this->send_user_id),
                'time' => $at,
                'name' => $this->live_name,
                'agent_id' => strval(0),
                'photo_path' => $this->photo_path,
                'notice' => $this->live_notice,
                'is_msg' => 0,
                'is_photo' => 0,
                'update_time' => $at
            ]);
            if (!$res) {
                return false;
            }
            //2.创建会话列表
            $res = ChatList::create([
                'user_id' => intval($this->send_user_id),
                'list_id' => $list_id,
                'user_ids' => $user_list_json,
                'status' => 0,
                'type' => intval($this->goods_type),
                'goods_id' => intval($this->goods_id),
                'top' => 1,
                'top_time' => 0,
                'no_reader_num' => 0,
                'ignore' => 0,
                'temporary' => 0,
                'past_time' => 0,
                'created_at' => $at,
                'updated_at' => $at
            ]);
            if (!$res) {
                return false;
            }
            //3.添加会话成员
            $resM = ChatMember::create([
                'list_id' => $list_id,
                'user_id' => intval($this->send_user_id),
                'nickname' => '',
                'is_admin' => 0,
                'is_msg' => 0,
                'is_onLine' => 1,
                'time' => $at,
                'created_at' => $at,
                'updated_at' => $at
            ]);
            if (!$resM) {
                return false;
            }
            /** 创建系统默认消息 */
            $chatRow = Chat::create([
                'list_id' => $list_id,
                'user_id' => $this->send_user_id,
                'content_type' => 0,
                'msg_type' => MessagePush::MESSAGE_USER, //系统消息
                'content' => [
                    'text' => $this->live_notice,
                ],
                'time' => $at,
            ]);

            return $list_id;
        } catch (\Exception $exception) {
            return false;
        }
    }


    /**
     * 创建临时会话
     * @return bool|string
     */
    public function temporaryChat()
    {
        try {
            $at = time();
            $to_user_id = $this->to_user_id;
            if ($this->mch_id) {//有mchId则查询商户绑定的用户
                $mch = Mch::getOne($this->mch_id);
                if ($mch) {
                    $to_user_id = $mch->user_id;
                }
            }
            $user_list = [intval($this->send_user_id), intval($to_user_id)];
            sort($user_list);
            $user_list_json = json_encode($user_list);
            //验证是否存在会话
            $has = ChatList::select('list_id')
                ->where('type', intval(8))
                ->where('user_ids', $user_list_json)
                ->first();
            if ($has) {
                return $has->list_id;
            }
            //1.创建会话
            $list_id = self::getListId();
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
                $res = ChatList::create([
                    'user_id' => $val,
                    'list_id' => $list_id,
                    'user_ids' => $user_list_json,
                    'status' => 0,
                    'type' => 8,
                    'goods_id' => 0,
                    'top' => 1,
                    'top_time' => 0,
                    'no_reader_num' => $no_reader_num,
                    'ignore' => 0,
                    'temporary' => 1,
                    'past_time' => intval($at + 180),
                    'created_at' => $at,
                    'updated_at' => $at
                ]);
                if (!$res) {
                    return false;
                }
                //4.添加会话成员
                $resM = ChatMember::create([
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
                if (!$resM) {
                    return false;
                }

            }
            //5.创建系统默认消息
            $chatRow = Chat::create([
                'list_id' => $list_id,
                'user_id' => intval($this->send_user_id),
                'content_type' => MessagePush::CONTENT_SYS_DEFAULT,
                'msg_type' => MessagePush::MESSAGE_USER,
                'content' => [
                    'text' => '当前为临时对话,3分钟后自动删除',
                ],
                'time' => $at,
            ]);
            return $list_id;
        } catch (\Exception $exception) {
            return false;
        }
    }


    /**
     * 创建联盟会话
     * @return bool|string
     */
    public function unionChat()
    {
        try {
            $union = Union::getOne($this->union_id);
            if (!$union) {
                return false;
            }
            $list_id = '';
            if ($union->list_id) {
                return $union->list_id;
            } else {
                $list_id = self::getListId();
                $up_list_id = DB::table(Union::tableName)->where('id', $union->id)->update([
                    'list_id' => $list_id,
                    'updated_at' => date("Y-m-d H:i:s")
                ]);
                if (!$up_list_id) {
                    return false;
                }
            }
            $at = time();
            $user_list = [intval($this->send_user_id)];
            $user_list_json = json_encode($user_list);
            //创建会话
            //1.创建会话列表
            $res = ChatList::create([
                'user_id' => $this->send_user_id,
                'list_id' => $list_id,
                'user_ids' => $user_list_json,
                'status' => 0,
                'type' => 9,
                'goods_id' => 0,
                'top' => 1,
                'top_time' => 0,
                'no_reader_num' => 1,
                'ignore' => 0,
                'temporary' => 0,
                'past_time' => 0,
                'created_at' => $at,
                'updated_at' => $at
            ]);
            if (!$res) {
                return false;
            }
            //2.添加会话成员
            $resM = ChatMember::create([
                'list_id' => $list_id,
                'user_id' => intval($this->send_user_id),
                'nickname' => '',
                'is_admin' => 0,
                'is_msg' => 0,
                'is_onLine' => 1,
                'time' => $at,
                'created_at' => $at,
                'updated_at' => $at
            ]);
            if (!$resM) {
                return false;
            }
            //3.创建联盟
            $resU = ChatUnion::create([
                'union_id' => $union->id,
                'list_id' => $list_id,
                'union_sn' => $union->union_sn,
                'title' => $union->title,
                'slogan' => $union->slogan,
                'notice' => $union->notice,
                'leader_user_id' => $union->leader_user_id,
                'leader_user_name' => $union->leader_user_name,
                'level' => $union->level,
                'status' => $union->status,
                'share_number' => $union->share_number,
                'max_online_number' => $union->max_online_number,
                'report_number' => $union->report_number,
                'violation_number' => $union->violation_number,
                'unseal_number' => $union->unseal_number,
                'credit_score' => $union->credit_score,
                'max_admin_number' => $union->max_admin_number,
                'max_online_user' => $union->max_online_user,
                'is_delete' => $union->is_delete,
                'deleted_at' => $union->deleted_at,
                'created_at' => $union->created_at,
                'updated_at' => $union->updated_at,
            ]);
            if (!$resU) {
                return false;
            }
            //发送默认系统消息
            //5.创建系统默认消息
            Chat::create([
                'list_id' => $list_id,
                'user_id' => intval($this->send_user_id),
                'content_type' => MessagePush::CONTENT_SYS_DEFAULT,
                'msg_type' => MessagePush::MESSAGE_USER,
                'content' => [
                    'text' => $union->slogan ?: $union->notice ?: '欢迎来到《' . $union->title . '》联盟！',
                ],
                'time' => $at,
            ]);

            return $list_id;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * 获取会话列表ID
     * @return string
     */
    private static function getListId()
    {
        return md5(uniqid('JWT', true) . rand(1, 100000));
    }


    public function sendText(){

    }
}
