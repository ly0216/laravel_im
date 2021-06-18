<?php

namespace App\Http\Controllers;

use App\Mongodb\Chat;
use App\Mongodb\ChatGroup;
use App\Mongodb\ChatList;
use App\Mongodb\Friend;
use App\Mongodb\MemberFd;
use App\Mysql\AskBuy;
use App\Mysql\Goods;
use App\Mysql\GoodsWarehouse;
use App\Mysql\Mch;
use App\Mysql\Store;
use App\Mysql\UserInfo;
use App\User;
use Illuminate\Http\Request;

class SwController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.jwt');
    }

    //
    public function push()
    {
        $user_id = auth('api')->id();
        $ids = [strval($user_id)];

        $fd_list = MemberFd::select('fd_id')->whereIn('user_id', $ids)->get();
        $data = [
            'type' => 1,
            'content_type' => 0,
            'content' => [
                'text' => '这特么就是一个测试的消息，没别的意思。就是告诉在坐的各位都是垃圾！'
            ],
            'send_at' => date("Y-m-d H:i:s")
        ];
        $swoole = app('swoole');
        if ($fd_list) {
            foreach ($fd_list as $item => $val) {
                if ($swoole->isEstablished($val['fd_id'])) {
                    $swoole->push($val['fd_id'], json_encode($data));
                }
            }
        }

        return response()->json($data);
    }

    public function getChatList()
    {
        $return = ['err' => 0, 'message' => 'success', 'data' => []];
        try {
            $user_id = auth('api')->id();

            $list = ChatList::select('list_id', 'user_ids', 'no_reader_num', 'type', 'top', 'top_time', 'goods_id')
                ->where('user_id', $user_id)
                ->where('status', 0)->get()->toArray();
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
                                    ->where('user_id',$user_id)
                                    ->where('friend_id',$friend_id)
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
                                        if($goods){
                                            $objRow['id'] = $goods->id;
                                            $objRow['price'] = $goods->price;
                                            $goods_warehouse = GoodsWarehouse::getOne($goods->goods_warehouse_id);
                                            if($goods_warehouse){
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
                'err' => 0,
                'data' => array_merge($top_data, $chat_other_data),
                'msg' => 'success',
            ];
            return response()->json($return);

        } catch (\Exception $exception) {
            return response()->json([
                'err' => 1,
                'msg' => 'fail',
                'data' => $exception->getMessage(),
                'line' => $exception->getLine()
            ]);
        }
    }
}
