<?php

namespace App\Http\Controllers;

use App\Common\Code;
use App\Im\Common;
use App\Models\ConversationModel;
use App\Models\LunarCalendar;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    /**
     * 会话管理控制器
     */

    public function __construct()
    {
        $this->middleware('check.token', ['except' => ['getCnDate']]);
    }

    public function getCnDate()
    {
        $week_arr = ["日","一","二","三","四","五","六"];
        $week = date('w', strtotime('2021-07-19'));
        $data = LunarCalendar::solarToLunar('2021', '7', '19');
        return response()->json(['code' => Code::HTTP_SUCCESS, 'message' => '会话创建成功', 'data' => ['week' => $week_arr[$week], 'lunar' => $data]]);
    }

    /**
     * 创建会话
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        try {
            $type = $request->post('type');
            if (!in_array($type, [1, 2, 3, 4, 5, 6])) {
                throw new \Exception('无效的会话类型');
            }
            $to_user_id = $request->post('user_id');
            if (!$to_user_id) {
                throw new \Exception('缺少参数');
            }
            $user_id = auth('api')->id();
            if (!$user_id) {
                throw new \Exception('登录信息已失效');
            }
            $list_id = '';
            switch ($type) {
                case 1:
                    //创建客服会话
                    $cvsModel = new ConversationModel();
                    $cvsModel->send_user_id = $user_id;
                    $list_id = $cvsModel->customerService();
                    if (!$list_id) {
                        throw new \Exception('添加客服会话失败');
                    }
                    break;
                case 2:
                    //创建用户私聊会话
                    $cvsModel = new ConversationModel();
                    $cvsModel->send_user_id = $user_id;
                    $cvsModel->to_user_id = $to_user_id;
                    $list_id = $cvsModel->privateChat();
                    if ($list_id == 'no') {
                        throw new \Exception('还不是好友');
                    }
                    if (!$list_id) {
                        throw new \Exception('创建会话失败');
                    }
                    break;
                case 3:
                    //创建商品会话
                    $goods_type = $request->post('goods_type');
                    if (!in_array($goods_type, [4, 5])) {
                        throw new \Exception('商品类型错误');
                    }
                    $goods_id = $request->post('goods_id');
                    if (!$goods_id) {
                        throw new \Exception('缺少商品ID');
                    }
                    $mch_id = $request->post('mch_id') ?: 0;

                    $cvsModel = new ConversationModel();
                    $cvsModel->send_user_id = $user_id;
                    $cvsModel->to_user_id = $to_user_id;
                    $cvsModel->goods_type = $goods_type;
                    $cvsModel->goods_id = $goods_id;
                    $cvsModel->mch_id = $mch_id;
                    $list_id = $cvsModel->goodsChat();
                    if (!$list_id) {
                        throw new \Exception('创建会话失败');
                    }
                    break;
                case 4:
                    //创建直播会话
                    $live_name = $request->post('live_name');
                    if (!$live_name) {
                        throw new \Exception('直播名称不能为空');
                    }
                    $live_notice = $request->post('live_notice');
                    if (!$live_notice) {
                        throw new \Exception('直播介绍不能为空');
                    }
                    $live_label = $request->post('live_label_id');
                    if (!$live_label) {
                        throw new \Exception('请选择直播标签');
                    }
                    $photo_path = $request->post('photo_path') ?: '';
                    $cvsModel = new ConversationModel();
                    $cvsModel->send_user_id = $user_id;
                    $cvsModel->to_user_id = $to_user_id;
                    $cvsModel->live_name = $live_name;
                    $cvsModel->live_notice = $live_notice;
                    $cvsModel->live_label = $live_label;
                    $cvsModel->photo_path = $photo_path;
                    $list_id = $cvsModel->liveChat();
                    break;
                case 5:
                    //创建临时会话
                    $mch_id = $request->post('mch_id') ?: 0;
                    $cvsModel = new ConversationModel();
                    $cvsModel->send_user_id = $user_id;
                    $cvsModel->to_user_id = $to_user_id;
                    $cvsModel->mch_id = $mch_id;
                    $list_id = $cvsModel->temporaryChat();
                    break;
                case 6:
                    $union_id = $request->post('union_id');
                    if (!$union_id) {
                        throw new \Exception('联盟ID不能为空');
                    }
                    //创建联盟会话
                    $cvsModel = new ConversationModel();
                    $cvsModel->send_user_id = $user_id;
                    $cvsModel->union_id = $union_id;
                    $list_id = $cvsModel->unionChat();
                    break;
            }

            return response()->json(['code' => Code::HTTP_SUCCESS, 'message' => '会话创建成功', 'data' => ['list_id' => $list_id]]);
        } catch (\Exception $exception) {
            return response()->json(['code' => Code::HTTP_ERROR, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * 发送消息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        try {
            $list_id = $request->post('list_id');
            $content_type = $request->post('content_type') ?: 0;
            $content = $request->post('content');
            if (!$list_id || !$content_type) {
                return response()->json(['err' => Code::HTTP_ERROR, 'msg' => '缺少参数']);
            }
            if (!Common::is_json($content)) {
                return response()->json(['err' => Code::HTTP_ERROR, 'msg' => '参数格式错误']);
            }
            $user_id = auth('api')->id();
            $cvsModel = new ConversationModel();
            $cvsModel->send_user_id = $user_id;
            $cvsModel->content_type = $content_type;
            $cvsModel->content = $content;
            $msg_id = $cvsModel->sendText();
            return response()->json(['err' => Code::HTTP_SUCCESS, 'msg' => '消息发送成功', 'data' => [$msg_id]]);
        } catch (\Exception $exception) {
            return response()->json(['err' => Code::HTTP_ERROR, 'msg' => '消息发送失败']);
        }
    }
}
