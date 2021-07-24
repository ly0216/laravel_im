<?php

namespace App\Http\Controllers;

use App\Common\Code;
use App\Im\Common;
use App\Im\MessagePush;
use App\Models\MongoDB;
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
                'action'=>'chatMessage',
                'party_id' => intval($party->id),
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
    public function historyMessage(Request $request){
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
            $list = PartyMessage::select('*')->where('action','chatMessage')->where('party_id',intval($party->id))
                ->orderBy('created_at','desc')->limit(20)->get()->toArray();

            return response()->json(['code' => 0, 'message' => '成功','data'=>array_reverse($list)]);
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
                    'id' => MongoDB::getTableIdx(PartyMember::tableName,true),
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
                'action'=>'chatMessage',
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
