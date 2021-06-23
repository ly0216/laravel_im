<?php

namespace App\Http\Controllers;

use App\Mongodb\MemberFd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SwController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.jwt');
    }

    //
    public function push(Request $request)
    {
        $number = $request->post('number')?:20;
        $user_id = auth('api')->id();
        $ids = [13483, 13473, 13471, 13467, 13465];
        //$ids = [$user_id];
        $fd_list = MemberFd::getFd($ids); //select('fd_id')->whereIn('user_id',$ids)->get();
        $data = [
            'type' => 1,
            'content_type' => 0,
            'content' => [
                'text' => '这特么就是一个测试的消息，没别的意思。就是告诉在坐的各位都是垃圾！'
            ],
            'send_at' => date("Y-m-d H:i:s"),
            /*'fd_list' => $fd_list,
            'ids' => $ids*/
        ];

        for($i=0;$i<$number;$i++){
            $fd_list = MemberFd::getFd($ids);
            if ($fd_list) {
                $swoole = app('swoole');
                foreach ($fd_list as $item => $val) {
                    if ($swoole->isEstablished($val['fd_id'])) {
                        $data['user_id'] = $val['user_id'];
                        $swoole->push($val['fd_id'], json_encode($data));
                    }
                }
            }
            //usleep(500000);
        }


        return response()->json($data);
    }

}
