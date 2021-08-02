<?php

namespace App\Console\Commands;

use App\Mongodb\HeaderImages;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class InitImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:img';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新头像图片';

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
        $dir = 'images/header';
        $dir_list = Storage::disk('web-image')->allFiles($dir);
        $at = date("Y-m-d H:i:s");
        $new_number = 0;
        $rep_number = 0;
        for ($i = 0; $i < count($dir_list); $i++) {
            $has = HeaderImages::where('image_tail',$dir_list[$i])->first();
            if(!$has){
                HeaderImages::create([
                    'title'=>'暂无标题',
                    'apply_sex'=>2,//适用性别  1男  2女
                    'image_url'=>env('BASE_IMAGE_URL') . $dir_list[$i],
                    'image_tail'=>$dir_list[$i],
                    'used_number'=>1,
                    'created_at'=>$at,
                    'updated_at'=>$at
                ]);
                $new_number++;
                echo "新增：【{$dir_list[$i]}】\n";
            }else{
                $rep_number++;
            }
        }
        echo "新增【{$new_number}】个头像，有【{$rep_number}】个头像已经存在";

        return true;
    }
}
