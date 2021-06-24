<?php

namespace App\Http\Controllers;

use App\Common\Code;
use App\Models\ConversationModel;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    /**
     * 会话管理控制器
     */

    public function __construct()
    {
        $this->middleware('check.token', ['except' => ['sysCreate']]);
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
                    if(!$goods_id){
                        throw new \Exception('缺少商品ID');
                    }
                    $mch_id = $request->post('mch_id')?:0;

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
                case 4://直播
                    break;
                case 5://临时
                    break;
                case 6://联盟
                    break;
            }

            return response()->json(['code' => Code::HTTP_SUCCESS, 'message' => '会话创建成功', 'data' => ['list_id' => $list_id]]);
        } catch (\Exception $exception) {
            return response()->json(['code' => Code::HTTP_ERROR, 'message' => $exception->getMessage()]);
        }
    }
}
