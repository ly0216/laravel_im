<?php

namespace App\Http\Controllers;

use App\Common\Code;
use App\Mongodb\ChatGroupApply;
use App\Mongodb\ChatMember;
use App\Mongodb\FriendApply;
use App\Mysql\AliLiveAnchor;
use App\Mysql\GoodsBrand;
use App\Mysql\Order;
use Illuminate\Http\Request;
use App\Mongodb\Chat;
use App\Mongodb\ChatGroup;
use App\Mongodb\ChatList;
use App\Mongodb\Friend;
use App\Mysql\AskBuy;
use App\Mysql\Goods;
use App\Mysql\GoodsWarehouse;
use App\Mysql\Mch;
use App\Mysql\UserInfo;
use App\User;
use Illuminate\Support\Facades\DB;

class GetController extends Controller
{


    /**
     * 获取会话列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChatList(Request $request)
    {
        $return = ['err' => Code::HTTP_SUCCESS, 'message' => 'success', 'data' => []];
        try {
            $page_size = $request->post('page_size') ?: 20;

            $user_id = auth('api')->id();
            $list = ChatList::select('list_id', 'user_ids', 'no_reader_num', 'type', 'top', 'top_time', 'goods_id')
                ->where('user_id', $user_id)
                ->where('status', 0)
                ->simplePaginate($page_size);
            $top_data = [];
            $chat_other_data = [];
            $objRow = [];
            if ($list) {
                foreach ($list as $item => $val) {
                    $is_live = 0;
                    $mch_id = 0;

                    switch ($val['type']) {
                        case 0:
                        case 4:
                        case 5:
                        case 8:
                        case 6:
                            /** 对话 */
                            $chat_data = Chat::select('user_id', 'content_type', 'msg_type', 'content', 'time')
                                ->where('list_id', $val['list_id'])
                                ->orderBy('time', 'DESC')
                                ->first();
                            $val['user_ids'] = json_decode($val['user_ids']);
                            //好友
                            $friend_id = $val['user_ids'][0] == $user_id ? $val['user_ids'][1] : $val['user_ids'][0];
                            //获取用户的mch_id
                            $userMch = Mch::getUserMch($friend_id);

                            if ($userMch) {
                                $mch_id = $userMch->id;
                            }
                            if ($val['type'] == 0) {
                                $friend_data = Friend::select('remarks')
                                    ->where('user_id', $user_id)
                                    ->where('friend_id', $friend_id)
                                    ->first();
                            }

                            $user = User::getOne($friend_id);
                            $userInfo = UserInfo::getOne($friend_id);
                            //$store = Store::getOneByMch($user->mch_id);

                            /** 如果没有设置备注就显示用户昵称 */
                            if (isset($friend_data) && $friend_data->remarks) {
                                $show_name = $friend_data->remarks;
                            } else {
                                $show_name = empty($user->nickname) ? '' : $user->nickname;
                            }

                            $last_msg = '';
                            if ($chat_data) {
                                $last_msg = ChatList::chatType($chat_data->content_type, isset($chat_data['content']['text']) ? $chat_data['content']['text'] : '');
                                $time = $chat_data->time;
                            }
                            $photo_path = empty($userInfo->avatar) ? '' : $userInfo->avatar;

                            //查询对象数据(出售,求购,直播)
                            if (isset($val['goods_id']) && $val['goods_id'] != 0) {
                                switch ($val['type']) {
                                    case 4:
                                        $objRow = [];
                                        $goods = Goods::getOne($val['goods_id']);
                                        if ($goods) {
                                            $objRow['id'] = $goods->id;
                                            $objRow['price'] = $goods->price;
                                            $goods_warehouse = GoodsWarehouse::getOne($goods->goods_warehouse_id);
                                            if ($goods_warehouse) {
                                                $objRow['name'] = $goods_warehouse->name;
                                                $objRow['pic_url'] = $goods_warehouse->pic_url;
                                                $objRow['video_url'] = $goods_warehouse->video_url;
                                                $objRow['live_goods'] = $goods_warehouse->live_goods;
                                                $objRow['live_goods_status'] = $goods_warehouse->live_goods_status;
                                                $objRow['degree'] = $goods_warehouse->degree;
                                                $objRow['brand'] = $goods_warehouse->brand;
                                                $objRow['shop_price'] = $goods_warehouse->shop_price;
                                                $objRow['cover_pic'] = $goods_warehouse->cover_pic;
                                                $objRow['detail'] = $goods_warehouse->detail;
                                            }
                                        }

                                        $objRow['pic_url'] = json_decode($objRow['pic_url'], true) ?: [];
                                        break;
                                    case 5:
                                        $objRow = AskBuy::getOne($val['goods_id']);
                                        break;
                                    case 6:
                                        break;
                                }
                            } else {
                                $objRow = '';
                            }

                            break;
                        case 1:
                            /** 群聊 */
                            //先判断是不是群直播
                            $group_data = ChatGroup::select('list_id', 'main_id', 'name', 'is_photo', 'is_live')
                                ->where('list_id', $val['list_id'])
                                ->first();

                            if ($group_data && $group_data['is_live'] == 1) {
                                $is_live = 1;
                            }

                            $chat_data = Chat::select('user_id', 'content_type', 'msg_type', 'content', 'time')
                                ->where('list_id', $val['list_id'])
                                ->orderBy('time', 'DESC')
                                ->first();

                            $last_msg = ChatList::chatType($chat_data->content_type, isset($chat_data['content']['text']) ? $chat_data['content']['text'] : '');
                            $time = $chat_data->time;
                            $show_name = $group_data['name'];
                            if (isset($group_data['is_photo']) && $group_data['is_photo']) {
                                $photo_path = 'https://im.bopo.com/static/photo/group_photo/' . $val['list_id'] . '/90.jpg';
                            } else {
                                $photo_path = 'http://b-img-cdn.bopo.com/uploads/mall1/20210407/1bd14a8e1319abec2685f33b8c078393.png';
                            }

                            break;
                        case 2:
                            /** 系统消息 */
                            $last_msg = '';
                            $time = 0;
                            $show_name = '系统消息';
                            break;
                        case 3:
                            /** 公众号消息 */
                            $last_msg = '';
                            $time = '';
                            $show_name = 0;
                            break;

                        default:
                            /** 未知类消息 */
                            $last_msg = '';
                            $time = 0;
                            $show_name = '未知消息';
                            break;
                    }
                    if ($is_live == 1) {
                        continue;
                    }
                    $photo_path = '';
                    $data = [
                        'list_id' => $val['list_id'],
                        'no_reader_num' => $val['no_reader_num'],
                        'show_name' => $show_name,
                        'mch_id' => $mch_id,
                        'last_msg' => $last_msg,
                        'photo_path' => $photo_path,
                        'time' => $time,
                        'top' => $val['top'],
                        'top_time' => (isset($value['top_time']) ? $val['top_time'] : 0),
                        'type' => $val['type'],
                        'obj_row' => $objRow,
                        'show_option' => false
                    ];
                    if ($val['top']) {
                        $top_data[] = $data;
                    } else {
                        $chat_other_data[] = $data;
                    }
                }
            }
            /** 消息置顶的根据置顶时间来排序 */
            if (count($top_data)) {
                $is_field = array_column($top_data, 'top_time');
                array_multisort($is_field, SORT_DESC, $top_data);
            }
            /** 根据消息最后时间排序 */
            if (count($chat_other_data)) {
                $is_field = array_column($chat_other_data, 'time');

                array_multisort($is_field, SORT_DESC, $chat_other_data);
            }
            $return = [
                'err' => Code::HTTP_SUCCESS,
                'data' => array_merge($top_data, $chat_other_data),
                'msg' => 'success',
            ];
            return response()->json($return);

        } catch (\Exception $exception) {
            return response()->json([
                'err' => Code::HTTP_ERROR,
                'msg' => 'fail',
                'data' => $exception->getMessage(),
                'line' => $exception->getLine()
            ]);
        }
    }

    /**
     * 获取基础信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBase()
    {
        $return = ['err' => Code::HTTP_SUCCESS, 'message' => 'success', 'data' => []];
        try {
            $user_id = auth('api')->id();
            $user = User::getOne($user_id);
            $userInfo = UserInfo::getOne($user_id);
            $mchData = [];
            $mch = Mch::getUserMch($user_id);

            if ($mch) {
                $mchData['mch_id'] = $mch->id;
                $mchData['is_reputation'] = $mch->is_reputation;
                $mchData['reputation_level'] = $mch->reputation_level;
            }
            $user_state = [
                'no_reader_circle' => 0,
                'no_reader_circle_chat_num' => 0,
            ];
            $circle_img = 'default_circle_img.jpg';
            $photo = '';
            if ($userInfo) {
                $photo = $userInfo->avatar;
            }

            $new_group_tips_num = 0;

            /** 获得自己的所有群 */
            $group_data = ChatList::select('list_id')
                ->where([
                    'user_id' => $user_id,
                    'type' => 1,
                    'status' => 0,
                ])
                ->select();
            foreach ($group_data as $item) {
                $chat_member_data = ChatMember::select('list_id')
                    ->where([
                        'list_id' => $item->list_id,
                        'user_id' => $user_id,
                        'is_admin' => 1,
                    ])
                    ->find();
                if ($chat_member_data) {
                    $new_group_tips_num = ChatGroupApply::where('list_id', $item->list_id)->where('is_reader', 0)->count();
                }
            }

            //获取主播邀请信息
            $new_anchor_join = 0;

            $anchorData = AliLiveAnchor::getAnchorByUser($user_id);

            if ($anchorData) {
                $new_anchor_join = 1;
            }

            $new_friend_tips_num = FriendApply::where('friend_user_id', $user_id)->where('is_reader', 0)->where('action', 0)->count();
            $return['data'] = [
                /** 用户基础信息 */
                'user_info' => [
                    'id' => $user->id,
                    'nickname' => $user->nickname,
                    // 'username' => $user->username,
                    'username' => $user->mobile,
                    'photo' => $photo,
                    'doodling' => $user->doodling,
                    'sex' => $user->sex,
                    'circle_img' => $circle_img,
                ],
                'mch_data' => $mchData,
                /** 群消息认证 */
                'new_group_tips_num' => $new_group_tips_num,
                /** 通讯录新的朋友提示 */
                'new_friend_tips_num' => $new_friend_tips_num,
                /** 未读消息总数 */
                'no_reader_chat_num' => $this->getNoChatNum(),
                /** 朋友圈好友动态 */
                'no_reader_circle' => $user_state['no_reader_circle'],
                /** 朋友圈关于我的消息 */
                'no_reader_circle_chat_num' => $user_state['no_reader_circle_chat_num'],
                /** 主播入职消息提醒 */
                'new_anchor_join' => $new_anchor_join,
            ];

            return response()->json($return);
        } catch (\Exception $exception) {
            return response()->json([
                'err' => Code::HTTP_ERROR,
                'msg' => 'fail',
                'data' => $exception->getMessage(),
                'line' => $exception->getLine()
            ]);
        }
    }

    /**
     * 获取对话数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChatData(Request $request)
    {
        $return = ['err' => Code::HTTP_ERROR, 'msg' => 'success', 'data' => []];
        try {
            $user_id = auth('api')->id();
            $list_id = $request->post('list_id');
            $is_up = $request->post('is_up') ?: 0;
            $time = $request->post('time') ?: 0;
            $number = $request->post('number') ?: 15;
            if (!$list_id) {
                $return = ['err' => Code::HTTP_ERROR, 'msg' => '缺少会话ID', 'data' => []];
                return response()->json($return);
            }
            $db_chat_list = ChatList::select('list_id', 'user_ids', 'type', 'goods_id')
                ->where('list_id', $list_id)
                ->first();

            if (!$db_chat_list) {
                $return['msg'] = '这条对话不存在';
                return response()->json($return);
            }
            $db_chat_list['user_ids'] = json_decode($db_chat_list['user_ids'], true);
            $data = [];
            $map = [
                ['list_id', '=', $db_chat_list['list_id']],
            ];
            if ($time) {
                $map[] = ['time', '<', $time];
            }

            $number = intval($number);

            $db_data = Chat::where($map)
                ->orderBy('time DESC')
                ->limit($number)->get()->toArray();

            if (count($db_data)) {
                $db_data = array_reverse($db_data);

                $goods_data = [];
                $askBy_data = [];
                foreach ($db_data as $key => $value) {
                    $sex = '';
                    $mch_id = 0;
                    if ($value['user_id']) {
                        $user = User::getOne($value['user_id']);
                        if ($user) {
                            $sex = $user->sex;
                        }
                    }
                    //$user_state = UserState::select('photo')->where('user_id', $value['user_id'])->first();
                    //$face = $this->getShowPhoto($user_state, $sex, $value['user_id'], 50);
                    $order = [];
                    $goods_id = 0;
                    if(isset($value['content']['goods_id'])){
                        $goods_id = $value['content']['goods_id'];
                    }
                    if (($value['content_type'] == 11 && array_key_exists('goods_id', $value['content'])) || ($value['content_type'] == 26 && array_key_exists('goods_id', $value['content'])) || ($value['content_type'] == 30 && array_key_exists('goods_id', $value['content'])) || ($value['content_type'] == 12 && array_key_exists('goods_id', $value['content']))) { //加入购物车  商品置顶  出售
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
                    }


                    if ($value['content_type'] == 31 && array_key_exists('goods_id', $value['content'])) { //  求购
                        $askBuy = AskBuy::getOne($goods_id);
                        if ($askBuy) {
                            $goodsB = GoodsBrand::getOne($askBuy->brand_id);
                            $askBuy->brand = $goodsB;
                        }

                        /*$askBy_data = AskBuy::field('a.id,a.brand_id,a.content,a.degree,a.cover_pic,a.high_price,a.low_price,b.name brand_name')->alias('a')
                            ->join('goods_brand b', 'a.brand_id = b.id', 'LEFT')
                            ->where('a.id', $value['content']['goods_id'])
                            ->find();*/
                    }


                    if (($value['content_type'] == 12 || $value['content_type'] == 13 || $value['content_type'] == 14 || $value['content_type'] == 15 || $value['content_type'] == 16 || $value['content_type'] == 17 || $value['content_type'] == 18 || $value['content_type'] == 19 || $value['content_type'] == 20) && array_key_exists('order_no', $value['content'])) {

                        $order_data = Order::getSnAll($value['content']['order_no']);
                        /*$order_data = Order::field(['a.id', 'a.order_no', 'a.name', 'a.mobile', 'a.address', 'd.num', 'd.total_price', 'd.goods_info', 'd.total_store_price', 'd.is_store_price'])->alias("a")
                            ->where('a.order_no', $value['content']['order_no'])
                            ->join('order_detail d', 'a.id = d.order_id')
                            ->all()
                            ->toArray();*/

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
                    $user = User::getOne($value['user_id']);
                    $userInfo = UserInfo::getOne($value['user_id']);
                    //获取用户的mch_id

                    $mch_info = Mch::getUserMch($value['user_id']);
                    if ($mch_info) {
                        $mch_id = $mch_info->id;
                    }

                    if (in_array($value['content_type'], [32, 33, 34, 35])) {
                        $data_msg = [
                            'id' => $value['_id'],
                            'type' => $value['content_type'],
                            'time' => $value['time'],
                            'goods_data' => $goods_data,
                            'ab_data' => $askBy_data,
                            'order_data' => $order,
                            'user_info' => [
                                'uid' => $value['user_id'],
                                'name' => isset($user->nickname) ? $user->nickname : '',
                                'face' => isset($userInfo->avatar) ? $userInfo->avatar : '',
                                'mch_id' => $mch_id,
                                'is_store_user' => $mch_id ? 1 : 0
                            ],
                        ];
                        if ($value['content_type'] == 32) {
                            $data_msg['content'] = $value['content'];
                        } else {
                            $data_msg['content'] = $value['content']['content'];
                            $data_msg['auction_goods'] = $value['content']['auction_goods'];
                            $data_msg['auction_rule'] = $value['content']['auction_rule'];
                        }
                        $data[] = [
                            'type' => $value['msg_type'],
                            'msg' => $data_msg
                        ];
                    } else {

                        $data[] = [
                            'type' => $value['msg_type'],
                            'msg' => [
                                'id' => $value['_id'],
                                'type' => $value['content_type'],
                                'time' => $value['time'],
                                'goods_data' => $goods_data,
                                'ab_data' => $askBy_data,
                                'order_data' => $order,
                                'user_info' => [
                                    'uid' => $value['user_id'],
                                    'name' => isset($user->nickname) ? $user->nickname : '',
                                    'face' => isset($userInfo->avatar) ? $userInfo->avatar : '',
                                    'mch_id' => $mch_id,
                                    'is_store_user' => $mch_id ? 1 : 0
                                ],
                                'content' => $value['content'],
                            ],
                        ];
                    }
                }
            }

            /** 让未阅读数为0 */
            if ($is_up) {
                ChatList::where([
                    'list_id' => $db_chat_list['list_id'],
                    'user_id' => $user_id,
                ])->update([
                    'no_reader_num' => 0,
                ]);
            }
            $is_msg = 0;
            $is_action = 0;
            $obj_id = 0;
            switch ($db_chat_list->type) {
                case 0:
                case 4:
                case 5:
                case 8:
                case 6:
                    /** 如果有备注，显示备注，否则显示昵称 */
                    $obj_id = $db_chat_list['user_ids'][0] == $user_id ? $db_chat_list['user_ids'][1] : $db_chat_list['user_ids'][0];
                    $db_friend_data = Friend::select('remarks')
                        ->where([
                            'user_id' => $user_id,
                            'friend_id' => intval($obj_id),
                        ])
                        ->first();
                    $user = User::getOne($obj_id);
                    if (!$db_friend_data && $db_chat_list->type == 0) {
                        $show_name = '';
                        if ($user) {
                            $show_name = $user->nickname;
                        }
                    } else {
                        $show_name = $db_chat_list->type == 0 && $db_friend_data->remarks ? $db_friend_data->remarks : $user ? $user->nickname : '';
                    }
                    $is_action = 1;
                    //获取用户的mch_id

                    $mch_info = Mch::getUserMch($obj_id);
                    $mchId = 0;
                    if ($mch_info) {
                        $mchId = $mch_info->id;
                    }
                    break;
                case 1:
                    /** 显示群聊，群的名称 */
                    $bool = ChatList::select('user_id')
                        ->where([
                            'user_id' => $user_id,
                            'list_id' => $list_id
                        ])->first();

                    if (!$bool) {
                        //查询群类型
                        $groupType = ChatGroup::select('is_live')
                            ->where([
                                'list_id' => $list_id
                            ])->first();

                        if ($groupType['is_live'] == 0) {
                            //不是直播群
                            //$return_data = $this->liveGroup($post_data, $return_data, 1);
                        } else if ($groupType['is_live'] == 1) {
                            //直播群
                            // $return_data = $this->liveGroup($post_data, $return_data);
                        }
                    }


                    $group_data = ChatGroup::select('id,name,main_id,is_msg')
                        ->where('list_id', $db_chat_list['list_id'])
                        ->first();
                    $chat_member_count = ChatMember::where('list_id', $db_chat_list['list_id'])->count();
                    $show_name = $group_data['name'] . '(' . $chat_member_count . ')';
                    $is_msg = 0;
                    $chat_member_data = ChatMember::select('is_admin,is_msg')
                        ->where([
                            'list_id' => $db_chat_list['list_id'],
                            'user_id' => $user_id,
                        ])->first();
                    /** 如果禁言了，自己不是群主和管理员的话，就不能发言 */
                    if ($chat_member_data->is_msg || ($group_data->is_msg && $group_data->main_id != USER_ID && $chat_member_data->is_admin == 0)) {
                        $is_msg = 1;
                    }
                    /** 群主和管理员才能查看其他会员消息 */
                    if ($group_data->main_id == $user_id || $chat_member_data->is_admin) {
                        $is_action = 1;
                    }
                    break;
                case 2:
                    /** 显示系统通知消息 */
                    break;
                case 3:
                    /** 显示公众号 */
                    break;
                default:
                    $show_name = '';
                    break;
            }
            $return_data = [
                'err' => Code::HTTP_SUCCESS,
                'data' => [
                    'list_id' => $db_chat_list->list_id,
                    'type' => $db_chat_list->type,
                    'show_name' => $show_name,
                    'list' => $data,
                    'is_msg' => $is_msg,
                    'is_action' => $is_action,
                    'obj_id' => $obj_id,
                    'goods_id' => $db_chat_list->goods_id
                ],
            ];
            if ($db_chat_list->type != 1) {
                $return_data['data']['mch_id'] = $mchId;
            }

            return response()->json($return_data);
        } catch (\Exception $exception) {
            return response()->json([
                'err' => Code::HTTP_ERROR,
                'msg' => 'fail',
                'data' => $exception->getMessage(),
                'line' => $exception->getLine()
            ]);
        }
    }


    /**
     * 获得未读消息数
     * @return int
     */
    public function getNoChatNum()
    {
        $user_id = auth('api')->id();
        $num = ChatList::where('user_id', $user_id)->where('status', 0)->sum('no_reader_num');
        return $num;
    }

    /**
     * 获得显示的头像
     * @param $user_state_obj
     * @param $sex
     * @param $user_id
     * @param $size
     * @return string
     */
    public function getShowPhoto($user_state_obj, $sex, $user_id, $size)
    {
        if ($user_state_obj && $user_state_obj->photo) {
            $photo_path = 'user/' . $user_id . '/' . $size . '.jpg';
        } else {
            if ($sex) {
                $photo_path = 'default_woman/' . $size . '.jpg';
            } else {
                $photo_path = 'default_man/' . $size . '.jpg';
            }
        }
        return $photo_path;
    }
}
