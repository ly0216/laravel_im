<?php

namespace App\Http\Controllers;

use App\Common\Code;
use App\Im\Common;
use App\Im\MessagePush;
use App\Models\MongoDB;
use App\Mongodb\Collection;
use App\Mongodb\DaySign;
use App\Mongodb\PartyList;
use App\Mongodb\PartyMember;
use App\Mongodb\PartyMessage;
use Illuminate\Http\Request;

class HomeController extends Controller
{


    public function __construct()
    {
        $this->middleware('check.token');
    }

    public function collectionDel(Request $request)
    {
        try {

            $user_id = auth('api')->id();
            if (!$user_id) {
                return response()->json(['code' => 1, 'message' => '无效的用户']);
            }
            $coll_id = $request->post('collection_id');
            if (!$coll_id) {
                return response()->json(['code' => 1, 'message' => '缺少参数']);
            }
            $has = Collection::where('_id', $coll_id)->first();
            if (!$has) {
                return response()->json(['code' => 1, 'message' => '无效的收藏']);
            }
            if ($has->user_id != $user_id) {
                return response()->json(['code' => 1, 'message' => '删除失败']);
            }
            $del = Collection::where('_id', $coll_id)->update([
                'is_delete' => 1,
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            if(!$del){
                return response()->json(['code' => 1, 'message' => '删除失败']);
            }
            return response()->json(['code' => 0, 'message' => '成功', 'data' => []]);
        } catch (\Exception $exception) {
            return response()->json(['code' => 1, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * 我的收藏列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function collectionList(Request $request)
    {
        try {
            $user_id = auth('api')->id();
            if (!$user_id) {
                return response()->json(['code' => 1, 'message' => '无效的用户']);
            }
            $page = $request->post('page') ?: 1;
            $limit = 20;
            $skip = ($page - 1) * $limit;
            $model = Collection::select('_id', 'user_id', 'party_id', 'is_top', 'created_at')
                ->where('user_id', intval($user_id))
                ->where('is_delete', 2);
            $total = $model->count();
            $total_page = ceil($total / $limit);
            $list = $model->orderBy('is_top', 'desc')
                ->orderBy('created_at', 'desc')
                ->skip($skip)->limit($limit)->get();
            foreach ($list as $item => $val) {
                $party = PartyList::select('user_avatar', 'title', 'content', 'chat_sn')->where('_id', $val->party_id)
                    ->where('is_delete', 0)->where('status', 1)->first();
                if (!$party) {
                    $val->leader_user_avatar = env('DEFAULT_AVATAR', 'https://images.jobslee.top/storage/images/header/not_found.jpg');
                    $val->party_title = '无效的派对';
                    $val->party_content = '无效的派对';
                    $val->chat_sn = 'no';
                } else {
                    $val->leader_user_avatar = $party->user_avatar;
                    $val->party_title = $party->title;
                    $val->party_content = $party->content;
                    $val->chat_sn = $party->chat_sn;
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

    /**
     * 收藏/取消收藏
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function collection(Request $request)
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
            $party = PartyList::where('chat_sn', $chat_sn)->first();
            if (!$party) {
                return response()->json(['code' => 1, 'message' => '派对不存在']);
            }
            $at = date("Y-m-d H:i:s");
            $is_coll = 2;
            $tip = '收藏成功';
            $hasColl = Collection::where('user_id', intval($user_id))->where('party_id', $party->_id)->first();
            if (!$hasColl) {
                $res = Collection::create([
                    'user_id' => intval($user_id),
                    'party_id' => $party->_id,
                    'is_top' => 2,
                    'is_delete' => 2,
                    'created_at' => $at,
                    'updated_at' => $at
                ]);
                if (!$res) {
                    return response()->json(['code' => 2, 'message' => '收藏失败', 'data' => []]);
                }
                $is_coll = 1;
            } else {
                if ($hasColl->is_delete == 1) {
                    $res = Collection::where('_id', $hasColl->_id)->update([
                        'is_delete' => 2,
                        'updated_at' => $at
                    ]);
                    if (!$res) {
                        return response()->json(['code' => 2, 'message' => '收藏失败', 'data' => []]);
                    }
                    $is_coll = 1;
                } else {
                    $res = Collection::where('_id', $hasColl->_id)->update([
                        'is_delete' => 1,
                        'updated_at' => $at
                    ]);
                    if (!$res) {
                        return response()->json(['code' => 2, 'message' => '取消收藏失败', 'data' => []]);
                    }
                    $tip = '取消收藏成功';
                }
            }


            return response()->json(['code' => 0, 'message' => $tip, 'data' => $is_coll]);
        } catch (\Exception $exception) {
            return response()->json(['code' => 1, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * 随机加入派对
     * @return \Illuminate\Http\JsonResponse
     */
    public function randomJoin()
    {
        try {

            $user_id = auth('api')->id();
            if (!$user_id) {
                return response()->json(['code' => 1, 'message' => '无效的用户']);
            }
            $list = PartyList::where('is_delete', 0)->where('status', 1)->pluck('chat_sn');
            $number = count($list);
            $idx = mt_rand(0, ($number - 1));
            $chat_sn = $list[$idx];
            return response()->json(['code' => 0, 'message' => '成功', 'data' => $chat_sn]);
        } catch (\Exception $exception) {
            return response()->json(['code' => 1, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * 创建派对
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createParty(Request $request)
    {
        try {

            $user_id = auth('api')->id();
            if (!$user_id) {
                return response()->json(['code' => 1, 'message' => '无效的用户']);
            }
            $user = auth('api')->user();
            if ($user->can_create == 2) {
                return response()->json(['code' => 1, 'message' => '你还没有创建派对的权限']);
            }
            $title = $request->post('title');
            $content = $request->post('content');
            $background_image = $request->post('background_image');
            if (!$title || !$content || !$background_image) {
                return response()->json(['code' => 1, 'message' => '请将信息填写完整']);
            }
            $hasEx = PartyList::where('user_id', intval($user_id))->where('status', 0)->first();
            if ($hasEx) {
                return response()->json(['code' => 1, 'message' => '你还有没审核的派对，无法继续创建派对']);
            }
            $party_number = PartyList::where('user_id', intval($user_id))->count();
            if ($party_number > $user->party_limit) {
                return response()->json(['code' => 1, 'message' => "你最多只能创建{$user->party_limit}个派对"]);
            }
            $at = date("Y-m-d H:i:s");
            $status = 0;
            if ($user->need_examine == 2) {
                $status = 1;
            }
            $data = [
                'id' => MongoDB::getTableIdx(PartyList::tableName),
                'chat_sn' => Common::getChatSn(),
                'status' => intval($status),
                'user_id' => intval($user_id),
                'user_name' => $user->user_nickname,
                'user_avatar' => $user->user_avatar,
                'title' => $title,
                'content' => $content,
                'background_image' => $background_image,
                'online_number' => 0,
                'views' => 0,
                'is_delete' => 0,
                'created_at' => $at,
                'updated_at' => $at
            ];
            $res = PartyList::create($data);
            if (!$res) {
                return response()->json(['code' => 1, 'message' => '派对创建失败']);
            }

            return response()->json(['code' => 0, 'message' => '成功', 'data' => []]);
        } catch (\Exception $exception) {
            return response()->json(['code' => 1, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * 我创建的派对
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function myRoomList(Request $request)
    {
        try {

            $user_id = auth('api')->id();
            if (!$user_id) {
                return response()->json(['code' => 1, 'message' => '无效的用户']);
            }
            $page = $request->post('page') ?: 1;
            $skip = ($page - 1) * 20;
            $list = PartyList::select('*')->where('user_id', intval($user_id))->where('is_delete', 0)
                ->orderBy('created_at', 'desc')
                ->skip($skip)->limit(20)->get();
            return response()->json(['code' => 0, 'message' => '成功', 'data' => $list]);
        } catch (\Exception $exception) {
            return response()->json(['code' => 1, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * 派对列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function roomList(Request $request)
    {
        try {

            $user_id = auth('api')->id();
            if (!$user_id) {
                return response()->json(['code' => 1, 'message' => '无效的用户']);
            }
            $page = $request->post('page') ?: 1;
            $search_text = $request->post('search_text');
            $is_hot = $request->post('is_hot') ?: 0;
            $skip = ($page - 1) * 20;
            $party = PartyList::select('*')->where('is_delete', 0)->where('status', 1);
            if ($search_text) {
                $party->where('title', 'like', "%$search_text%");
            }
            $count = $party->count();
            $pages = ceil($count / 20);
            if ($is_hot) {
                $party->orderBy('views', 'desc');
            }
            $list = $party->orderBy('created_at', 'desc')
                ->skip($skip)->limit(20)->get();
            $return = [
                'page' => $page,
                'pages' => $pages,
                'list' => $list
            ];
            return response()->json(['code' => 0, 'message' => '成功', 'data' => $return]);
        } catch (\Exception $exception) {
            return response()->json(['code' => 1, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * 获取派对详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function roomDetail(Request $request)
    {
        try {
            $chat_sn = $request->post('chat_sn');
            if (!$chat_sn) {
                return response()->json(['code' => 1, 'message' => '缺少参数']);
            }
            $user_id = auth('api')->id();
            if (!$user_id) {
                return response()->json(['code' => 1, 'message' => '无效的用户']);
            }

            $party = PartyList::where('chat_sn', $chat_sn)->first();
            if (!$party) {
                return response()->json(['code' => 1, 'message' => '派对不存在']);
            }
            $hasColl = Collection::where('user_id', intval($user_id))->where('party_id', $party->_id)->where('is_delete', 2)->count();
            $party->is_collection = $hasColl;
            PartyList::where('chat_sn', $chat_sn)->increment('views', 1);
            return response()->json(['code' => 0, 'message' => '成功', 'data' => $party]);
        } catch (\Exception $exception) {
            return response()->json(['code' => 1, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * 发送消息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(Request $request)
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
            $party = PartyList::where('chat_sn', $chat_sn)->first();
            if (!$party) {
                return response()->json(['code' => 1, 'message' => '派对不存在']);
            }
            $user = auth('api')->user();
            $at = date("Y-m-d H:i:s");
            $message = [
                'id' => MongoDB::getTableIdx(PartyMessage::tableName),
                'action' => 'chatMessage',
                'party_id' => intval($party->id),
                'chat_sn' => $party->chat_sn,
                'user_id' => intval($user_id),
                'user_name' => $user->user_nickname,
                'user_avatar' => $user->user_avatar,
                'content' => json_decode($content, true),
                'message_type' => PartyMessage::MESSAGE_TYPE_USER,
                'content_type' => PartyMessage::CONTENT_TYPE_TEXT,
                'created_at' => $at,
                'updated_at' => $at
            ];
            PartyMessage::create($message);
            MessagePush::send($party->id, $message);
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
            $chat_sn = $request->post('chat_sn');
            if (!$chat_sn) {
                return response()->json(['code' => 1, 'message' => '缺少参数']);
            }
            $user_id = auth('api')->id();
            if (!$user_id) {
                return response()->json(['code' => 1, 'message' => '无效的用户']);
            }

            //1.派对是否存在
            $party = PartyList::where('chat_sn', $chat_sn)->first();
            if (!$party) {
                return response()->json(['code' => 1, 'message' => '派对不存在']);
            }
            $list = PartyMessage::select('*')->where('action', 'chatMessage')->where('party_id', intval($party->id))
                ->orderBy('created_at', 'desc')->limit(20)->get()->toArray();

            return response()->json(['code' => 0, 'message' => '成功', 'data' => array_reverse($list)]);
        } catch (\Exception $exception) {
            return response()->json(['code' => 1, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * 进入派对
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function joinRoom(Request $request)
    {
        try {
            $chat_sn = $request->post('chat_sn');
            if (!$chat_sn) {
                return response()->json(['code' => 1, 'message' => '缺少参数']);
            }
            $user_id = auth('api')->id();
            if (!$user_id) {
                return response()->json(['code' => 1, 'message' => '无效的用户']);
            }
            $user = auth('api')->user();
            $at = date("Y-m-d H:i:s");
            //1.派对是否存在
            $party = PartyList::where('chat_sn', $chat_sn)->first();
            if (!$party) {
                return response()->json(['code' => 1, 'message' => '派对不存在']);
            }
            //2.是否已经加入派对
            $join = PartyMember::where('party_id', intval($party->id))->where('user_id', intval($user_id))->first();
            if (!$join) {
                PartyMember::create([
                    'id' => MongoDB::getTableIdx(PartyMember::tableName, true),
                    'party_id' => intval($party->id),
                    'user_id' => intval($user_id),
                    'user_name' => $user->user_nickname,
                    'user_avatar' => $user->user_avatar,
                    'is_online' => 1,
                    'is_tick' => 0,
                    'created_at' => $at,
                    'updated_at' => $at
                ]);
            } else {
                if ($join->is_online == 0) {
                    PartyMember::where('party_id', intval($party->id))->where('user_id', intval($user_id))
                        ->update([
                            'is_online' => 1,
                            'updated_at' => $at
                        ]);
                }
            }
            //3.发送消息
            $message = [
                'id' => MongoDB::getTableIdx(PartyMessage::tableName),
                'action' => 'chatMessage',
                'chat_sn' => $party->chat_sn,
                'party_id' => intval($party->id),
                'user_id' => intval($user_id),
                'user_name' => $user->user_nickname,
                'user_avatar' => $user->user_avatar,
                'content' => ['text' => $user->user_nickname . ' 进来跟大家聊天啦'],
                'message_type' => PartyMessage::MESSAGE_TYPE_SYS,
                'content_type' => PartyMessage::CONTENT_TYPE_TEXT,
                'created_at' => $at,
                'updated_at' => $at
            ];
            PartyMessage::create($message);
            MessagePush::send($party->id, $message);
            return response()->json(['code' => 0, 'message' => '成功']);
        } catch (\Exception $exception) {
            return response()->json(['code' => 1, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * 获取首页心情日签数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDaySign()
    {
        $list = DaySign::select('id', 'user_id', 'user_name', 'user_avatar', 'views', 'lunar_year', 'cn_month', 'cn_day', 'bron_year', 'week', 'content')
            ->where('is_delete', 2)->orderBy('views', 'desc')->limit(3)->get();
        return response()->json(['code' => Code::HTTP_SUCCESS, 'message' => '获取成功', 'data' => $list]);
    }
}
