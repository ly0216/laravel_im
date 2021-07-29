<?php
/**
 * Created by PhpStorm.
 * User: liyang
 * Date: 2021/7/28
 * Time: 4:42 PM
 */
namespace App\CronTab;
use Hhxsv5\LaravelS\Swoole\Timer\CronJob;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Swoole\Coroutine;

class AuctionCronJob extends CronJob
{
    // !!! 定时任务的`interval`和`isImmediate`有两种配置方式（二选一）：一是重载对应的方法，二是注册定时任务时传入参数。
    // --- 重载对应的方法来返回配置：开始
    public function interval()
    {
        return 1000;// 每1秒运行一次
    }
    public function isImmediate()
    {
        return true;// 是否立即执行第一次，false则等待间隔时间后执行第一次
    }
    // --- 重载对应的方法来返回配置：结束
    public function run()
    {


        $list = Cache::get('cache_auction_list');
        // do something
        // sleep(1); // Swoole < 2.1
        Coroutine::sleep(5); // Swoole>=2.1 run()方法已自动创建了协程。
        if($list){
            foreach ($list as $item =>$val){
                $now_at = time() + mt_rand(10,30);
                $ago_at = $now_at - $val['auction_begin_at'] ;
                if($ago_at >= 0){
                    if($ago_at >= $val['auction_redis_ex']){
                        Log::channel('auction')->info("{$val['auction_redis_key']} 已结束");
                    }else{
                        $end_limit = $val['auction_redis_ex'] - $ago_at;
                        Log::channel('auction')->info("{$val['auction_redis_key']} 还有{$end_limit}秒终止");
                    }
                }

            }
        }

        //Log::channel('auction')->info(date("Y-m-d H:i:s"));


       // if ($this->i >= 9) { // 运行10次后不再执行

            //Log::channel('auction')->info("stop {$this->i} ");
            //$this->stop(); // 终止此定时任务，但restart/reload后会再次运行
            // CronJob中也可以投递Task，但不支持Task的finish()回调。
            // 注意：修改config/laravels.php，配置task_ipc_mode为1或2，参考 https://wiki.swoole.com/#/server/setting?id=task_ipc_mode
            /*$ret = Task::deliver(new TestTask('task data'));
            var_dump($ret);*/
       // }
        // 此处抛出的异常会被上层捕获并记录到Swoole日志，开发者需要手动try/catch
    }
}

