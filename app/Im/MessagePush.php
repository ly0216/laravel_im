<?php

namespace App\Im;

use App\Common\Code;
use App\Mongodb\Chat;
use App\Mongodb\ChatGroup;
use App\Mongodb\ChatList;
use App\Mongodb\ChatMember;
use App\Mongodb\MemberFd;
use App\Mysql\AskBuy;
use App\Mysql\Goods;
use App\Mysql\GoodsBrand;
use App\Mysql\GoodsWarehouse;
use App\Mysql\Order;
use App\Mysql\UserInfo;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class MessagePush extends Model
{

    const MESSAGE_USER = 0;
    const MESSAGE_SYSTEM = 1;
    const MESSAGE_ADMIN = 2;

    static $MESSAGE_TYPE_LIST = [
        self::MESSAGE_USER => '用户消息',
        self::MESSAGE_SYSTEM => '系统消息',
        self::MESSAGE_ADMIN => '管理员消息'
    ];


    const CONTENT_SYS_DEFAULT = 0;    //会话系统提示消息
    const CONTENT_JOIN_CART = 11;    //成功加入购物车
    const CONTENT_BUY_NOT_PAY = 12;    //拍下未付款
    const CONTENT_ONLINE_PAY = 13;    //在线支付成功 支付宝 微信
    const CONTENT_CASH_DELIVERY = 14;    //货到付款
    const CONTENT_APPLY_REFUND = 16;    //申请退款
    const CONTENT_CONFIRM_RECEIPT = 17;    //确认收货
    const CONTENT_AFTER_REFUSE = 18;    //售后申请被拒绝
    const CONTENT_AFTER_AGREE = 19;    //售后申请已同意
    const CONTENT_REFUND_FINISH = 20;    //退款完成
    const CONTENT_ON_LINE = 21;    //用户进入房间
    const CONTENT_OFF_LINE = 22;    //用户离开房间
    const CONTENT_HAPPY_GET = 24;    //喜提
    const CONTENT_GOODS_SORT = 25;    //修改商品排序
    const CONTENT_GOODS_TOP = 26;    //置顶商品
    const CONTENT_GOODS_UP = 27;    //上架商品
    const CONTENT_GOODS_DOWN = 28;    //下架商品
    const CONTENT_GOODS_CHANGE = 29;    //商品修改
    const CONTENT_GOODS_WANTO = 30;    //想要商品
    const CONTENT_GOODS_HAVE = 31;    //有这个商品
    const CONTENT_AUCTION_BEGIN = 32;    //开始拍卖商品
    const CONTENT_AUCTION_SUCCESS = 33;    //商品拍卖成功--被拍出
    const CONTENT_AUCTION_OFFER = 35;    //竞拍出价
    const CONTENT_GOODS_CANCEL_BUY = 36;    //取消购买商品
    const CONTENT_GOODS_CANCEL_TOP = 37;    //取消商品置顶


    static $CONTENT_TYPE_LIST = [
        self::CONTENT_SYS_DEFAULT => '会话系统提示消息',
        self::CONTENT_JOIN_CART => '成功加入购物车',
        self::CONTENT_BUY_NOT_PAY => '拍下未付款',
        self::CONTENT_ONLINE_PAY => '在线支付成功 支付宝 微信',
        self::CONTENT_CASH_DELIVERY => '货到付款',
        self::CONTENT_APPLY_REFUND => '申请退款',
        self::CONTENT_CONFIRM_RECEIPT => '确认收货',
        self::CONTENT_AFTER_REFUSE => '售后申请被拒绝',
        self::CONTENT_AFTER_AGREE => '售后申请已同意',
        self::CONTENT_REFUND_FINISH => '退款完成',
        self::CONTENT_ON_LINE => '用户进入房间',
        self::CONTENT_OFF_LINE => '用户离开房间',
        self::CONTENT_HAPPY_GET => '喜提',
        self::CONTENT_GOODS_SORT => '修改商品排序',
        self::CONTENT_GOODS_TOP => '置顶商品',
        self::CONTENT_GOODS_UP => '上架商品',
        self::CONTENT_GOODS_DOWN => '下架商品',
        self::CONTENT_GOODS_CHANGE => '商品修改',
        self::CONTENT_AUCTION_BEGIN => '开始拍卖商品',
        self::CONTENT_AUCTION_SUCCESS => '商品拍卖成功--被拍出',
        self::CONTENT_AUCTION_OFFER => '竞拍出价',
        self::CONTENT_GOODS_CANCEL_BUY => '取消购买商品',
        self::CONTENT_GOODS_CANCEL_TOP => '取消商品置顶',
    ];

    /**
     * 组合消息
     * @param $send_user_id
     * @param $list_id
     * @param $msg_type
     * @param $content_type
     * @param $content
     * @return array
     */
    public static function pushMessage($send_user_id, $list_id, $msg_type, $content_type, $content)
    {
        try {
            $at = time();
            $return = ['code' => Code::HTTP_SUCCESS, 'msg' => 'success'];
            if ($content_type == self::CONTENT_OFF_LINE) {//用户离开房间
                $off_line = ChatMember::where('list_id', $list_id)->where('user_id', $send_user_id)
                    ->update([
                        'is_onLine' => 0,
                        'updated_at' => $at
                    ]);
                if (!$off_line) {
                    throw  new \Exception('离线失败');
                }
                return $return;
            }
            $chat_list = ChatList::select('_id', 'type', 'status')->where('list_id', $list_id)->first();
            if (!$chat_list) {
                throw new \Exception('没有这条会话，发送消息失败!aaa' . $list_id);
            }

            $type = self::MESSAGE_USER;
            if (strtolower($msg_type) == 'sys') {
                $type = self::MESSAGE_SYSTEM;
            } elseif (strtolower($msg_type) == 'is_admin') {
                $type = self::MESSAGE_ADMIN;
            }

            switch ($chat_list->type) {
                case 0:
                    break;
                case 1:
                    /**
                     * 如果是群聊禁言中不能发送消息
                     */
                    $chat_group = ChatGroup::select('is_msg', 'main_id', 'is_live')->where('list_id', $list_id)->first();
                    $chat_member_data = ChatMember::select('is_admin', 'is_msg')
                        ->where('list_id', $list_id)
                        ->where('user_id', $send_user_id)
                        ->first();
                    if (!$chat_group || !$chat_member_data) {
                        throw new \Exception('没有这条会话，发送消息失败!');
                    }
                    if ($chat_group->is_live == 1) {
                        if ($chat_member_data->is_msg && $msg_type == 0) {
                            return ['err' => Code::HTTP_PROHIBIT, 'msg' => '禁言了...'];
                        }
                    }
                    break;
                case 4:
                case 5:
                case 6:
                    /**
                     * 检测是否是临时会话
                     */
                    $chatListRow = ChatList::select('_id', 'user_id', 'user_ids', 'type', 'goods_id', 'temporary', 'list_id')
                        ->where('list_id', $list_id)->first()->toArray();

                    if (isset($chatListRow['temporary']) && $chatListRow['temporary'] == 1) {
                        //获取会话成员ID
                        $user_ids = json_decode($chatListRow['user_ids'], true);

                        //针对自己和自己聊天
                        $userId = array_unique($user_ids);
                        $list_user_id = $userId;
                        $ids_num = count($userId);
                        if ($ids_num != 1) {
                            for ($i = 0; $i < $ids_num; $i++) {
                                if ($user_ids[$i] != $send_user_id) {
                                    $list_user_id = $user_ids[$i];
                                }
                            }
                        }
                        //修改临时状态
                        ChatList::where('list_id', $list_id)->update([
                            'temporary' => 0
                        ]);
                        //添加对方会话
                        ChatList::create([
                            'user_id' => $list_user_id,
                            'list_id' => $chatListRow['list_id'],
                            'user_ids' => $chatListRow['user_ids'],
                            'status' => 0,
                            'type' => 4,
                            'goods_id' => intval($chatListRow['goods_id']),
                            'top' => 1,
                            'top_time' => $at,
                            'no_reader_num' => 1,
                            'ignore' => 0,
                            'temporary' => 0,
                            'created_at' => $at,
                            'updated_at' => $at
                        ]);
                        /** 增加到成员表 */
                        ChatMember::create([
                            'list_id' => $chatListRow['list_id'],
                            'user_id' => $list_user_id,
                            'nickname' => '',
                            'is_admin' => 0,
                            'is_msg' => 0,
                            'time' => $at,
                            'created_at' => $at,
                            'updated_at' => $at
                        ]);
                        break;
                    }
                case 8:
                    /**
                     * 检测是否是临时会话（店铺私信）
                     */
                    $chatListRow = ChatList::select('_id', 'user_id', 'user_ids', 'type', 'goods_id', 'temporary', 'list_id')
                        ->where('list_id', $list_id)->first()->toArray();
                    if ($chatListRow['temporary'] == 1) {
                        //获取会员成员ID
                        $user_ids = json_decode($chatListRow['user_ids'], true);
                        //针对自己和自己聊天
                        $userId = array_unique($user_ids);
                        $list_user_id = $userId;
                        $ids_num = count($userId);
                        if ($ids_num != 1) {
                            for ($i = 0; $i < $ids_num; $i++) {
                                if ($user_ids[$i] != $send_user_id) {
                                    $list_user_id = $user_ids[$i];
                                }
                            }
                        }
                        //修改临时状态
                        ChatList::where('list_id', $list_id)->update([
                            'temporary' => 0
                        ]);
                        //添加对方会话
                        ChatList::create([
                            'user_id' => $list_user_id,
                            'list_id' => $chatListRow['list_id'],
                            'user_ids' => $chatListRow['user_ids'],
                            'status' => 0,
                            'type' => 4,
                            'goods_id' => intval($chatListRow['goods_id']),
                            'top' => 1,
                            'top_time' => $at,
                            'no_reader_num' => 1,
                            'ignore' => 0,
                            'temporary' => 0,
                            'created_at' => $at,
                            'updated_at' => $at
                        ]);
                        /** 增加到成员表 */
                        ChatMember::create([
                            'list_id' => $chatListRow['list_id'],
                            'user_id' => $list_user_id,
                            'nickname' => '',
                            'is_admin' => 0,
                            'is_msg' => 0,
                            'time' => $at,
                            'created_at' => $at,
                            'updated_at' => $at
                        ]);
                    }
                    break;
                default:
                    return ['err' => Code::HTTP_UNKNOWN, 'msg' => '未知对话类型'];
                    break;

            }
            if ($chat_list->status) {
                ChatList::where('list_id', $list_id)->update([
                    'status' => 0
                ]);
            }
            $ab_data = [];
            $goods_data = [];
            if ($content_type == self::CONTENT_ON_LINE) {
                //用户进入房间
                ChatMember::where('list_id', $list_id)->where('user_id', $send_user_id)->update([
                    'is_onLine' => 1
                ]);
                $content_type = 0;
            }
            $chat_obj = Chat::create([
                'list_id' => $list_id,
                'user_id' => intval($send_user_id),
                'content_type' => $content_type,
                'msg_type' => $type,
                'content' => $content,
                'time' => $at,
            ]);
            if (!$chat_obj) {
                throw new \Exception('创建会话消息失败');
            }

            $order = [];
            if (in_array($content_type, [11, 12, 13, 29, 30]) || ($content_type == 26 && $content['goods_id'])) {
                $goods_id = $content['goods_id'];
                $brand = 0;
                $brand_name = '';
                $cover_pic = '';
                $degree = '';
                $name = '';
                $price = 0;
                $shop_price = 0;
                $goods = Goods::getOne($goods_id);
                if ($goods) {
                    $mch_id = $goods->mch_id;
                    $price = $goods->price;
                    $goodsW = GoodsWarehouse::getOne($goods->goods_warehouse_id);
                    if ($goodsW) {
                        $name = $goodsW->name;
                        $cover_pic = $goodsW->cover_pic;
                        $degree = $goodsW->degree;
                        $shop_price = $goodsW->shop_price;
                        $brand = $goodsW->brand;
                        $goodsB = GoodsBrand::getOne($goodsW->brand);
                        if ($goodsB) {
                            $brand_name = $goodsB->name;
                        }
                    }
                }

                $goods_data = [
                    'id' => $goods_id,
                    'brand' => $brand,
                    'brand_name' => $brand_name,
                    'cover_pic' => $cover_pic,
                    'degree' => $degree,
                    'mch_id' => $mch_id,
                    'name' => $name,
                    'price' => $price,
                    'shop_price' => $shop_price
                ];
            } elseif ($content_type == 31) {
                $goods_id = $content['goods_id'];
                $ab_data = [];
                $ab_data['id'] = 0;
                $ab_data['brand_id'] = 0;
                $ab_data['content'] = '';
                $ab_data['degree'] = '';
                $ab_data['cover_pic'] = '';
                $ab_data['high_price'] = 0;
                $ab_data['low_price'] = 0;
                $ab_data['brand_name'] = '';
                $askBuy = AskBuy::getOne($goods_id);
                if ($askBuy) {
                    $ab_data['id'] = $askBuy->id;
                    $ab_data['brand_id'] = $askBuy->brand_id;
                    $ab_data['content'] = $askBuy->content;
                    $ab_data['degree'] = $askBuy->degree;
                    $ab_data['cover_pic'] = $askBuy->cover_pic;
                    $ab_data['high_price'] = $askBuy->high_price;
                    $ab_data['low_price'] = $askBuy->low_price;
                    $goodsB = GoodsBrand::getOne($askBuy->brand_id);
                    if ($goodsB) {
                        $ab_data['brand_name'] = $goodsB->name;
                    }
                }
            } elseif (in_array($content_type, [14, 15, 16, 17, 18, 19, 20, 24])) {
                $string = mb_substr($content['text'], 0, 2);
                if ($string != '恭喜' && isset($content['order_no'])) {
                    $order_data = Order::getSnAll($content['order_no']);
                    if ($order_data) {
                        foreach ($order_data as $keys => $val) {
                            $order[$keys]['id'] = $val->id;
                            $order[$keys]['order_no'] = $val->order_no;
                            $order[$keys]['name'] = $val->name;
                            $order[$keys]['mobile'] = $val->mobile;
                            $order[$keys]['address'] = $val->address;
                            $order[$keys]['num'] = $val->num;
                            $goods_info = json_decode($val->goods_info, true);
                            $order[$keys]['attr_name'] = $goods_info['attr_list'][0]['attr_group_name'] . ": " . $goods_info['attr_list'][0]['attr_name'];//规格
                            $order[$keys]['goods_name'] = $goods_info['goods_attr']['name'];
                            $order[$keys]['cover_pic'] = $goods_info['goods_attr']['cover_pic'];
                            $order[$keys]['is_store_price'] = $val['is_store_price'];
                            if ($val['is_store_price'] == 1) {
                                $order[$keys]['goods_price'] = $val['total_store_price'];
                            } else {
                                $order[$keys]['goods_price'] = $val['total_price'];
                            }
                        }
                    }
                }
            }

            $member = ChatMember::select('user_id')->where('list_id', $list_id)->get();
            if (!$member) {
                throw new \Exception('未找到通知对象');
            }
            foreach ($member as $item => $val) {
                if ($send_user_id != $val->user_id) {
                    ChatList::where('list_id', $list_id)->where('user_id', intval($val->user_id))->increment('no_reader_num', 1);
                }

                $user = User::getOne($send_user_id);
                $userInfo = UserInfo::getOne($send_user_id);

                $res = self::sendToUid($send_user_id, $val->user_id, 'chatData', [
                    'list_id' => $list_id,
                    'data' => [
                        'type' => $type,
                        'msg' => [
                            'id' => $chat_obj->_id,
                            'type' => $content_type,
                            'time' => $at,
                            'ab_data' => $ab_data,
                            'goods_data' => $goods_data,
                            'order_data' => $order,
                            'user_info' => [
                                'uid' => $send_user_id,
                                'name' => isset($user->nickname) ? $user->nickname : '',
                                'face' => isset($userInfo->avatar) ? $userInfo->avatar : '',
                            ],
                            'content' => $content

                        ]
                    ]
                ]);
                if (!$res) {
                    //写日志
                    $log = [
                        'send_user_id' => $send_user_id,
                        'to_user_id' => $val->user_id,
                        'type' => 'chatData',
                        'list_id' => $list_id,
                        'content' => $content
                    ];
                    Log::channel('push-message')->info(json_encode($log));
                }

                if ($content_type == 21) {
                    $res = self::sendToUid($send_user_id, $val->user_id, 'chatData', [
                        'list_id' => $list_id,
                        'data' => [
                            'type' => self::MESSAGE_ADMIN,
                            'msg' => [
                                'id' => $chat_obj->_id,
                                'type' => 27,
                                'time' => $at,
                                'ab_data' => $ab_data,
                                'goods_data' => $goods_data,
                                'order_data' => $order,
                                'user_info' => [
                                    'uid' => $send_user_id,
                                    'name' => isset($user->nickname) ? $user->nickname : '',
                                    'face' => isset($userInfo->avatar) ? $userInfo->avatar : '',
                                ],
                                'content' => $content

                            ]
                        ]
                    ]);
                    if (!$res) {
                        //写日志
                        $log = [
                            'send_user_id' => $send_user_id,
                            'to_user_id' => $val->user_id,
                            'type' => 'chatData',
                            'list_id' => $list_id,
                            'content' => $content
                        ];
                        Log::channel('push-message')->info(json_encode($log));
                    }
                }

            }
            return ['code' => Code::HTTP_SUCCESS, 'msg' => 'success'];
        } catch (\Exception $exception) {
            return [
                'code' => Code::HTTP_ERROR,
                'msg' => $exception->getMessage(),
            ];
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

    /**
     * 发送消息【ids】
     * @param $send_user_id
     * @param $to_user_id
     * @param $type
     * @param $data
     * @return bool
     */
    private static function sendToUid($send_user_id, $to_user_id, $type, $data)
    {
        Log::channel('push-message')->info('SID:[' . $send_user_id . '],TID:[' . $to_user_id . ']');
        if (!$data || !isset($data['list_id'])) {
            return false;
        }
        //检测用户是否离线
        if (ChatMember::where('list_id', $data['list_id'])->where('user_id', $to_user_id)->where('is_onLine', 0)->first()) {
            return false;
        }
        //发送个推消息--推送
        /*if ($send_user_id != $to_user_id) {
            GeTuiPush::sendMessageToUid($send_user_id, $to_user_id, $type, $data);
        }*/
        //发送IM消息
        $user_ids = [];
        if (!is_array($to_user_id)) {
            $user_ids = [$to_user_id];
        }
        $fd_list = MemberFd::getFd($user_ids);
        $swoole = app('swoole');
        if ($fd_list) {
            foreach ($fd_list as $item => $val) {
                if ($swoole->isEstablished($val['fd_id'])) {
                    $swoole->push($val['fd_id'], json_encode($data));
                    Log::channel('push-message')->info('FD:[' . $val['fd_id'] . ']');
                }
            }
        }
        return true;

    }


}
