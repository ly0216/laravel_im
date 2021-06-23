<?php

namespace App\Http\Controllers;

use App\Common\Code;
use App\Im\GeTuiPush;
use App\Im\MessagePush;
use App\Mongodb\Chat;
use App\Mongodb\ChatList;
use App\Mongodb\ChatMember;
use App\Mongodb\MemberFd;
use App\Mysql\AliLive;
use App\Mysql\AliLiveGoods;
use App\Mysql\Goods;
use App\Mysql\GoodsWarehouse;
use App\Mysql\Mch;
use App\Mysql\UserInfo;
use App\User;
use Illuminate\Http\Request;

class GoodsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth.jwt')->except(['updateGoods']);
    }

    /**
     * 商品会话消息创建
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createGoodsChat(Request $request)
    {
        try {
            $return_data = ['err' => Code::HTTP_ERROR, 'msg' => 'error'];
            $user_id = auth('api')->id();
            if (!$user_id) {
                $return_data['msg'] = '用户认证失败';
                return response()->json($return_data);
            }
            $to_user_id = $request->post('user_id');
            $goods_id = $request->post('goods_id');
            $type = $request->post('type');
            if (!$to_user_id || !$goods_id || !$type) {
                $return_data['err'] = '缺少必要的参数';
                return response()->json($return_data);
            }
            $mch_id = $request->post('mch_id') ?: 0;
            if ($mch_id) {
                $mch = Mch::getCacheKey($mch_id);
                if ($mch) {
                    $to_user_id = $mch->user_id;
                }
            }
            $list_id = md5(uniqid('JWT', true) . rand(1, 100000));
            $chat_user_ids = [$user_id, intval($to_user_id)];
            sort($chat_user_ids);
            $chat_user_ids = json_encode($chat_user_ids);
            //return response()->json($chat_user_ids);
            //查询是否已经存在会话信息
            $at = time();
            $chatObj = ChatList::select('list_id')->where([
                'user_ids' => $chat_user_ids,
                'goods_id' => intval($goods_id),
                'type' => intval($type),
            ])->first();
            if ($chatObj) {
                $return_data = [
                    'err' => Code::HTTP_SUCCESS, 'msg' => 'success', 'data' => $chatObj->list_id
                ];
                return response()->json($return_data);
            }

            $content_type = 30;
            $text = '我想要这个商品';
            if ($type == 5) {
                //求购
                $content_type = 31;
                $text = '我有这个商品';
            }

            /** 创建会话信息*/
            $userId_array = json_decode($chat_user_ids, true);
            $userId_array = array_unique($userId_array);

            foreach ($userId_array as $key => $val) {
                $no_reader_num = 0;
                if ($val != $user_id) {
                    $no_reader_num = 1;
                }
                $is_online = 1;
                if ($val == $user_id) {
                    $is_online = 1;
                }
                $chatListRow = ChatList::create([
                    'user_id' => $val,
                    'list_id' => $list_id,
                    'user_ids' => $chat_user_ids,
                    'status' => 0,
                    'type' => intval($type),
                    'goods_id' => intval($goods_id),
                    'top' => 0,
                    'top_time' => 0,
                    'no_reader_num' => $no_reader_num,
                    'ignore' => 0,
                    'temporary' => 0,
                    'past_time' => 0,
                    'created_at' => $at,
                    'updated_at' => $at
                ]);

                /** 增加到成员表 */
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
            /** 创建系统默认消息 */
            $chatRow = Chat::create([
                'list_id' => $list_id,
                'user_id' => intval($user_id),
                'content_type' => $content_type,
                'msg_type' => 1, //系统消息
                'content' => [
                    'text' => $text,
                    'goods_id' => intval($goods_id),
                ],
                'time' => $at,
                'created_at' => $at,
                'updated_at' => $at
            ]);
            //创建成功
            if ($chatListRow && $chatRow) {

                /*if ($to_user_id != $user_id) {
                    ChatList::where([
                        'list_id' => $list_id,
                        'user_id' => intval($to_user_id),
                    ])->increment('no_reader_num', 1);
                }*/

                $user = User::getOne($user_id);// User::field('nickname')->where(['id' => USER_ID])->find();
                $userInfo = UserInfo::getOne($user_id);//field('avatar')->where(['user_id' => USER_ID])->find();

                /**
                 * 个推消息--推送 ios android
                 */
                $res = GeTuiPush::sendMessageToUid($user_id, $to_user_id, 'chatData', [
                    'list_id' => $list_id,
                    'data' => [
                        'type' => 0,
                        'msg' => [
                            'id' => $chatRow->id,
                            'type' => $content_type,
                            'time' => time(),
                            'user_info' => [
                                'uid' => $user_id,
                                'name' => isset($user->nickname) ? $user->nickname : '',
                                'face' => isset($userInfo->avatar) ? $userInfo->avatar : ''
                            ],
                            'content' => json_encode(['text' => $text, 'goods_id' => intval($goods_id),]),
                        ],
                    ]
                ]);
                $return_data = [
                    'err' => 0,
                    'data' => $list_id,
                    'msg' => 'success',
                    'res' => $res
                ];
                return response()->json($return_data);
            }


            return response()->json($chatObj);
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
     * 商品相关消息推送
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function pushGoodsMessage(Request $request)
    {
        try {
            $return_data = ['err' => Code::HTTP_ERROR, 'msg' => 'error'];
            $send_user_id = auth('api')->id();
            $list_id = $request->post('list_id');
            $goods_id = $request->post('goods_id');
            $type = $request->post('type');
            if (!$list_id || !$goods_id || !$type) {
                throw new \Exception('缺少参数');
            }
            $chat_list = ChatList::select('_id', 'type', 'status')->where('list_id', $list_id)->first();
            if (!$chat_list) {
                throw new \Exception('没有这条会话,发送消息失败!');
            }
            $content_type = 30;
            $text = '我想要这个商品';
            if ($type == 5) {//求购
                $content_type = 31;
                $text = '我有这个商品';
            }
            $content = [
                'text' => $text,
                'goods_id' => intval($goods_id)
            ];

            //发送消息
            $res = MessagePush::pushMessage($send_user_id, $list_id, '', $content_type, $content);
            if ($res['code'] > 0) {
                if ($res['code'] > 1) {
                    return response()->json([
                        'err' => Code::HTTP_PROHIBIT,
                        'msg' => $res['msg']
                    ]);
                }
                throw new \Exception($res['msg']);
            }
            $return_data = ['err' => Code::HTTP_SUCCESS, 'msg' => 'success'];
            return response()->json($return_data);
        } catch (\Exception $exception) {
            return response()->json([
                'err' => Code::HTTP_ERROR,
                'msg' => $exception->getMessage(),
                'line' => $exception->getLine()
            ]);
        }
    }

    /**
     * 商品信息修改消息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateGoods(Request $request)
    {
        try {
            $return_data = ['err' => Code::HTTP_ERROR, 'msg' => 'error'];
            $goods_id = $request->post('goods_id');
            if (!$goods_id) {
                $return_data['msg'] = '缺少参数';
                return response()->json($return_data);
            }
            //1.直播间内的商品
            $live_list_id = '';
            $goods_name = '';
            $live_goods = AliLiveGoods::getOneByGoods($goods_id);
            if ($live_goods) {
                $live = AliLive::getOne($live_goods->ali_live_id);
                if ($live) {
                    if (in_array($live->status, [3, 4, 7])) {
                        //校验商品是否已被购买--通过库存验证
                        $goods = Goods::getOne($goods_id, true);
                        if ($goods) {
                            if ($goods->goods_stock > 0) {
                                $goodsW = GoodsWarehouse::getOne($goods->goods_warehouse_id);
                                if($goodsW){
                                    $goods_name = $goodsW->name;
                                    $live_list_id = $live->list_id;
                                }

                            }
                        }
                    }
                }
            }
            if ($live_list_id) {//已上架直播间
                $data = [
                    'list_id' => $live_list_id,
                    'content_type' => MessagePush::CONTENT_GOODS_CHANGE,
                    'content' => json_encode([
                        'text' => '管理员对商品'.$goods_name.'进行了修改',
                        'goods_id' => $goods_id
                    ])
                ];
            }

            //2.私聊店铺的商品


        } catch (\Exception $exception) {
            return response()->json([
                'err' => Code::HTTP_ERROR,
                'msg' => $exception->getMessage(),
                'line' => $exception->getLine()
            ]);
        }
    }
}
