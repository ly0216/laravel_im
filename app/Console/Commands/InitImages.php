<?php

namespace App\Console\Commands;

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
        for ($i = 0; $i < count($dir_list); $i++) {
            $arr = [
                'path' => env('IMG_URL') . $dir_list[$i],
                'dir' => $dir_list[$i]
            ];
            $list[] = $arr;

        }
        var_dump($dir_list);
        return true;
    }
}
