<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SwController extends Controller
{
    //
    public function push()
    {
        $data = [
            'type' => 1,
            'content' => '这特么就是一个测试的消息，没别的意思。就是告诉在坐的各位都是垃圾！'
        ];
        for($i = 1;$i<=10;$i++){
            $swoole = app('swoole');
            if($swoole->isEstablished($i)){
                $swoole->push($i, json_encode($data));
            }


        }

        return response()->json($data);

    }
}
