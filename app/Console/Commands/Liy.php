<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ReflectionExtension;


class Liy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'l:y';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        /*$arr = [
            [1,2],
            [3,4]
        ];
        foreach($arr as  list($a,$b)){
            echo "A:{$a};B:$b \n";
        }*/
        //self::showMessage();
        //$this->pushMessage();
        //echo base_path('app/Http');
        $this->liy();
        return true;
    }

    public function liy(){
        echo "   _                  _        __      __ \n";
        echo "  | |                | |       \ \    / / \n";
        echo "  | |                | |        \ \  / /  \n";
        echo "  | |                | |         \ \/ /     \n";
        echo "  | |                | |          |  |     \n";
        echo "  | |                | |          |  |     \n";
        echo "  | |                | |          |  |     \n";
        echo "  | |________        | |          |  |     \n";
        echo "  |__________|       |_|          |__|     \n";



    }


    /**
     * 广播消息
     */
    public function pushMessage()
    {
        //$ext = new ReflectionExtension('swoole');
        //dd($ext->getFunctions());
        $data = [
            'type' => 1,
            'content' => '这特么就是一个测试的消息，没别的意思。就是告诉在坐的各位都是垃圾！'
        ];
        $fd =1;
        $swoole =app('swoole');
        $success = $swoole->push($fd,json_encode($data));
        dd($success);
    }

    //版本信息
    public function showMessage()
    {
        echo "版本信息：\n";
        echo "PHP版本：" . PHP_VERSION . "\n";
        echo "已安装扩展：\n";
        $ex_list = get_loaded_extensions();
        if (is_array($ex_list)) {
            $idx = 1;
            foreach ($ex_list as $item => $val) {
                if ($val != 'mysqlnd') {
                    if ($val == 'swoole'){

                        $ext = new ReflectionExtension($val);
                        var_dump($ext->getFunctions());
                    }
                    $ext = new ReflectionExtension($val);
                    echo "[{$idx}]《" . $val . " V" . $ext->getVersion() . "》\n";
                    $idx++;
                }
            }
        }
    }
}
