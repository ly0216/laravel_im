<?php

namespace App\Http\Controllers;

use App\Im\Common;
use App\Im\MessagePush;
use App\Mongodb\Friend;
use App\Mongodb\FriendApply;
use App\Mongodb\PartyMessage;
use App\Mongodb\PrivateChat;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FriendController extends Controller
{
    public function __construct()
    {
        $this->middleware('check.token');
    }

    /**
     * 发送消息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        try {
            $user_id = auth('api')->id();
            if (!$user_id) {
                return response()->json(['code' => 1, 'message' => '无效的用户']);
            }
            $content = $request->post('content');
            if (!$content) {
                return response()->json(['code' => 1, 'message' => '缺少参数']);
            }
            if (!Common::is_json($content)) {
                return response()->json(['code' => 1, 'message' => '参数错误']);
            }
            $chat_sn = $request->post('chat_sn');
            if (!$chat_sn) {
                return response()->json(['code' => 1, 'message' => '缺少参数']);
            }
            $has = Friend::select('*')->where('chat_sn', $chat_sn)->where('user_id', intval($user_id))->first();
            if (!$has) {
                return response()->json(['code' => 1, 'message' => '聊天不存在']);
            }
            $unread = Friend::select('_id')->where('chat_sn', $chat_sn)->where('user_id', intval($has->friend_id))->first();
            if($unread){
                Friend::where('_id',$unread->_id)->increment('unread_number',1);
            }
            $user = auth('api')->user();
            $at = date("Y-m-d H:i:s");

            $message = [
                'chat_sn' => $chat_sn,
                'user_id' => intval($user_id),//发送人的ID
                'user_name' => $user->user_nickname,
                'user_avatar' => $user->user_avatar,
                'user_ids_str' => $has->user_ids_str,
                'content' => json_decode($content, true),
                'message_type' => 0,
                'content_type' => 0,
                'is_delete' => 2,
                'created_at' => $at,
                'updated_at' => $at
            ];
            $res = PrivateChat::create($message);
            $message['_id'] = $res->_id;
            MessagePush::sendPrivate($chat_sn, $message);
            return response()->json(['code' => 0, 'message' => '成功']);
        } catch (\Exception $exception) {
            return response()->json(['code' => 1, 'message' => $exception->getMessage()]);
        }
    }


    /**
     * 历史消息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function historyMessage(Request $request)
    {
        try {

            $user_id = auth('api')->id();
            if (!$user_id) {
                return response()->json(['code' => 1, 'message' => '无效的用户']);
            }
            $chat_sn = $request->post('chat_sn');
            if (!$chat_sn) {
                return response()->json(['code' => 1, 'message' => '缺少参数']);
            }
            $has = Friend::select('*')->where('chat_sn', $chat_sn)->where('user_id', intval($user_id))->first();
            if (!$has) {
                return response()->json(['code' => 1, 'message' => '聊天不存在']);
            }
            $list = PrivateChat::select('*')->where('chat_sn', $chat_sn)->where('is_delete', 2)
                ->orderBy('created_at', 'desc')->limit(20)->get()->toArray();
            return response()->json(['code' => 0, 'message' => '成功', 'data' => array_reverse($list)]);
        } catch (\Exception $exception) {
            return response()->json(['code' => 1, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * 进入聊天
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function joinRoom(Request $request)
    {
        try {
            $user_id = auth('api')->id();
            if (!$user_id) {
                return response()->json(['code' => 1, 'message' => '无效的用户']);
            }
            $chat_sn = $request->post('chat_sn');
            if (!$chat_sn) {
                return response()->json(['code' => 1, 'message' => '缺少参数']);
            }
            $has = Friend::select('*')->where('chat_sn', $chat_sn)->where('user_id', intval($user_id))->first();
            if (!$has) {
                return response()->json(['code' => 1, 'message' => '聊天不存在']);
            }
            Friend::where('_id', $has->_id)->update([
                'unread_number' => 0
            ]);
            $user = User::getOne($has->friend_id);
            $title = '私聊会话';
            if ($user) {
                $title = $user->user_nickname;
            }
            return response()->json(['code' => 0, 'message' => '成功', 'data' => $title]);
        } catch (\Exception $exception) {
            return response()->json(['code' => 1, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * 好友列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function friendList(Request $request)
    {
        try {
            $user_id = auth('api')->id();
            if (!$user_id) {
                return response()->json(['code' => 1, 'message' => '无效的用户']);
            }
            $page = $request->post('page') ?: 1;
            $model = Friend::select('*')->where('user_id', intval($user_id));
            $total = $model->count();
            $limit = 20;
            $total_page = ceil($limit / $total);
            $skip = ($page - 1) * $limit;
            $list = $model->orderBy('created_at', 'desc')->skip($skip)->limit($limit)->get();
            foreach ($list as $item => $val) {
                $friend = User::getOne($val->friend_id);
                if ($friend) {
                    $val->user_avatar = $friend->user_avatar;
                    $val->user_nickname = $friend->user_nickname;
                }
                $last_message = PrivateChat::select('*')->where('chat_sn', $val->chat_sn)->orderBy('created_at', 'desc')->first();
                if ($last_message) {

                    $val->text = $last_message->content['text'];
                    $val->last_at = $this->getLastAtText($last_message->created_at);
                } else {
                    $val->text = '';
                    $val->lat_at = '';
                }
            }
            $data = [
                'total' => $total,
                'total_page' => $total_page,
                'page' => $page,
                'page_size' => $limit,
                'list' => $list
            ];
            return response()->json(['code' => 0, 'message' => '成功', 'data' => $data]);
        } catch (\Exception $exception) {
            return response()->json(['code' => 1, 'message' => $exception->getMessage()]);
        }
    }

    private function getLastAtText($last_at)
    {
        $at = strtotime($last_at);
        $tip = '刚刚';
        $now = time();

        $diff = $now - $at;
        $m = ceil($diff / 60);
        if ($diff > 60) {
            if ($m > 1 && $m < 60) {
                $tip = $m . "分钟前";
            } else {
                $h = ceil($m / 60);
                if ($h > 1 && $h < 24) {
                    $tip = $h . "小时前";
                } else {
                    $d = ceil($h / 24);
                    $tip = $d . "天前";
                }

            }
        }
        return $tip;
    }

    /**
     * 处理好友申请
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function friendApplyDo(Request $request)
    {
        try {
            $user_id = auth('api')->id();
            if (!$user_id) {
                return response()->json(['code' => 1, 'message' => '无效的用户']);
            }
            $apply_id = $request->post('apply_id');
            if (!$apply_id) {
                return response()->json(['code' => 1, 'message' => '缺少参数']);
            }
            $type = $request->post('type');
            if (!in_array($type, [1, 2])) {
                return response()->json(['code' => 1, 'message' => '无效的参数']);
            }
            $has = FriendApply::where('_id', $apply_id)->first();
            if (!$has) {
                return response()->json(['code' => 1, 'message' => '无效的申请']);
            }
            $at = date("Y-m-d H:i:s");
            if ($type == 1) {//同意
                $res = FriendApply::where('_id', $apply_id)->update([
                    'status' => 1,
                    'updated_at' => $at
                ]);
                if (!$res) {
                    return response()->json(['code' => 1, 'message' => '操作失败']);
                }
                //1.创建私聊会话
                $list = json_decode($has->user_ids_str, true);
                $chat_sn = Common::getChatSn();
                foreach ($list as $uid) {
                    //2.互相添加好友
                    if ($uid == $user_id) {
                        Friend::create([
                            'user_id' => intval($has->friend_id),
                            'user_ids_str' => $list,
                            'friend_id' => intval($has->user_id),
                            'chat_sn' => $chat_sn,
                            'unread_number' => 0,
                            'remarks' => '',
                            'created_at' => $at,
                            'updated_at' => $at
                        ]);
                    } else {
                        Friend::create([
                            'user_id' => intval($has->user_id),
                            'user_ids_str' => $list,
                            'friend_id' => intval($has->friend_id),
                            'chat_sn' => $chat_sn,
                            'unread_number' => 1,
                            'remarks' => '',
                            'created_at' => $at,
                            'updated_at' => $at
                        ]);
                    }
                }
                $user = User::getOne($user_id);
                //4.创建会话消息
                PrivateChat::create([
                    'chat_sn' => $chat_sn,
                    'user_id' => $user_id,//发送人的ID
                    'user_name' => $user->user_nickname,
                    'user_avatar' => $user->user_avatar,
                    'user_ids_str' => $list,
                    'content' => ['text' => '我们已经是好友啦，快来聊天吧'],//消息内容
                    'message_type' => 0,
                    'content_type' => 0,
                    'is_delete' => 2,
                    'created_at' => $at,
                    'updated_at' => $at
                ]);


            } else {
                $res = FriendApply::where('_id', $apply_id)->delete();
                if (!$res) {
                    return response()->json(['code' => 1, 'message' => '操作失败']);
                }
            }

            return response()->json(['code' => 0, 'message' => '操作成功', 'data' => []]);
        } catch (\Exception $exception) {
            return response()->json(['code' => 1, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * 好友申请列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function friendApplyList(Request $request)
    {
        try {
            $user_id = auth('api')->id();
            if (!$user_id) {
                return response()->json(['code' => 1, 'message' => '无效的用户']);
            }
            $page = $request->post('page') ?: 1;
            $model = FriendApply::select('*')->where('status', 2)->where('friend_id', intval($user_id));
            $total = $model->count();
            $limit = 20;
            $total_page = ceil($limit / $total);
            $skip = ($page - 1) * $limit;
            $list = $model->orderBy('created_at', 'desc')->skip($skip)->limit($limit)->get();
            $data = [
                'total' => $total,
                'total_page' => $total_page,
                'page' => $page,
                'page_size' => $limit,
                'list' => $list
            ];
            return response()->json(['code' => 0, 'message' => '成功', 'data' => $data]);
        } catch (\Exception $exception) {
            return response()->json(['code' => 1, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * 好友申请数量
     * @return \Illuminate\Http\JsonResponse
     */
    public function messageNumber()
    {
        try {
            $user_id = auth('api')->id();
            if (!$user_id) {
                return response()->json(['code' => 1, 'message' => '无效的用户']);
            }
            $number = FriendApply::where('friend_id', intval($user_id))->where('status', 2)->count();
            $message = Friend::where('user_id', intval($user_id))->sum('unread_number');
            $data = [
                'apply_number' => $number,
                'message_number' => $message
            ];
            return response()->json(['code' => 0, 'message' => '成功', 'data' => $data]);
        } catch (\Exception $exception) {
            return response()->json(['code' => 1, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * 添加好友请求
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function friendApply(Request $request)
    {
        try {

            $user_id = auth('api')->id();
            if (!$user_id) {
                return response()->json(['code' => 1, 'message' => '无效的用户']);
            }
            $friend_id = $request->post('friend_id');
            if (!$friend_id) {
                return response()->json(['code' => 1, 'message' => '缺少参数']);
            }
            $hasUser = DB::table(User::tableName)->select('id', 'user_nickname', 'user_avatar')->where('id', $friend_id)->first();
            if (!$hasUser) {
                return response()->json(['code' => 1, 'message' => '无效的用户']);
            }
            $my = auth('api')->user();
            $ids = [intval($user_id), intval($friend_id)];
            sort($ids);
            $str = json_encode($ids);
            $is_friend = Friend::where('user_id', $user_id)->where('friend_id', intval($friend_id))->first();
            if ($is_friend) {
                return response()->json(['code' => 1, 'message' => '已经是好友了']);
            }
            $hasApply = FriendApply::where('user_id', intval($user_id))
                ->where('friend_id', intval($friend_id))
                ->first();
            $at = date("Y-m-d H:i:s");
            if ($hasApply) {
                if ($hasApply->status == 1) {
                    FriendApply::where('_id', $hasApply->_id)->update([
                        'status' => 2,
                        'updated_at' => $at
                    ]);
                } else {
                    return response()->json(['code' => 1, 'message' => '请勿重复发送邀请']);
                }

            } else {
                FriendApply::create([
                    'user_id' => intval($user_id),
                    'user_ids_str' => $str,
                    'friend_id' => intval($friend_id),
                    'friend_nickname' => $my->user_nickname,
                    'friend_avatar' => $my->user_avatar,
                    'status' => 2,
                    'created_at' => $at,
                    'updated_at' => $at
                ]);
            }
            return response()->json(['code' => 0, 'message' => '成功', 'data' => ['ids' => $ids, 'srt' => $str]]);
        } catch (\Exception $exception) {
            return response()->json(['code' => 1, 'message' => $exception->getMessage()]);
        }
    }
}
