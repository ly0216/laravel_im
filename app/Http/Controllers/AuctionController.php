<?php

namespace App\Http\Controllers;

use App\CronTab\AuctionCronJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AuctionController extends Controller
{

    public function create(Request $request)
    {
        $number = $request->post('number') ?: 5;
        $list = [];
        $limit_at = 90;

        for ($i = 1; $i <= $number; $i++) {
            $at = time();
            $item = [
                'auction_redis_key' => 'cache_auction_name_99_13408_' . mt_rand(12001,43990),
                'auction_begin_at' => $at,
                'auction_redis_ex' => $limit_at
            ];
            array_push($list, $item);
        }
        Cache::forget('cache_auction_list');
        /*Cache::rememberForever('cache_auction_list', function () use ($list) {
            return $list;
        });*/
        $cache_list = Cache::get('cache_auction_list');
        return response()->json(['code' => 0, 'message' => 'ok', 'data' => $cache_list]);
    }

    public function reload(){
        $job = new AuctionCronJob();
        $job->interval();
    }
}
