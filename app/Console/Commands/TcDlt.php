<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TcDlt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tc:dlt';

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
        //
        self::upNo();
        //self::fenxi();
        die;
        $init_no = 200001;//14052;
        self::get_no($init_no);
    }

    static function get_no($no){
        $url = 'https://www.lottery.gov.cn/api/lottery_kj_detail_new.jspx?_ltype=4&_term=';
        for ($i = 0; $i < 160; $i++) {
            $next_no = $no + $i;
            $uri = $url . $next_no;
            dd($uri);
            $data = file_get_contents($uri);
            if ($data == "[{}]") {
                echo "No data";die;
            } else {
                $data = json_decode($data, true);
                $str = '';
                for ($j = 0; $j < 7; $j++) {
                    $str = $str . $data[0]['codeNumber'][$j] . " ";
                }
                echo $next_no . "::" . $str . "\n";
                $ins_data = [
                    'issue' => $next_no,
                    'win_num' => rtrim($str,' '),
                    'red_1' => $data[0]['codeNumber'][0],
                    'red_2' => $data[0]['codeNumber'][1],
                    'red_3' => $data[0]['codeNumber'][2],
                    'red_4' => $data[0]['codeNumber'][3],
                    'red_5' => $data[0]['codeNumber'][4],
                    'blue_1' => $data[0]['codeNumber'][5],
                    'blue_2' => $data[0]['codeNumber'][6],
                ];
                DB::table('history_no')->insert($ins_data);
            }
        }
    }

    static function fenxi(){
        $data = DB::table('history_no')->get();
        $arr = [
            'red1' => 0 ,
            'red2' => 0 ,
            'red3' => 0 ,
            'red4' => 0 ,
            'red5' => 0 ,
            'blue1' => 0 ,
            'blue2' => 0 ,
            ];
        foreach($data as $da){
            if($da->id < 656){
                $arr['red1'] = $arr['red1'] + $da->red_1;
                $arr['red2'] = $arr['red2'] + $da->red_2;
                $arr['red3'] = $arr['red3'] + $da->red_3;
                $arr['red4'] = $arr['red4'] + $da->red_4;
                $arr['red5'] = $arr['red5'] + $da->red_5;
                $arr['blue1'] = $arr['blue1'] + $da->blue_1;
                $arr['blue2'] = $arr['blue2'] + $da->blue_2;
            }else{
                echo $da->win_num."\n";
            }

        }
        foreach ($arr as $ar){
            echo (bcdiv($ar,656,18))."\n";
        }

    }

    static function upNo(){
        $url = 'https://www.lottery.gov.cn/api/lottery_kj_detail_new.jspx?_ltype=4&_term=';
        $issue = DB::table('history_no')->select('issue')->orderBy('id','desc')->first();
        $num = 0;
        if($issue->issue <= 18154){
            $num = 19001;
        }elseif($issue->issue == 19150){
            $num = 20001;
        }elseif($issue->issue == 20102){
            $num = 21001;
        }else{
            $num = $issue->issue + 1;
        }
        $uri = $url.$num;

        $data = file_get_contents($uri);
        if ($data == "[{}]") {
            echo "No data";die;
        } else {
            $data = json_decode($data, true);
            $str = '';
            for ($j = 0; $j < 7; $j++) {
                $str = $str . $data[0]['codeNumber'][$j] . " ";
            }
            $ins_data = [
                'issue' => $num,
                'win_num' => rtrim($str,' '),
                'red_1' => $data[0]['codeNumber'][0],
                'red_2' => $data[0]['codeNumber'][1],
                'red_3' => $data[0]['codeNumber'][2],
                'red_4' => $data[0]['codeNumber'][3],
                'red_5' => $data[0]['codeNumber'][4],
                'blue_1' => $data[0]['codeNumber'][5],
                'blue_2' => $data[0]['codeNumber'][6],
            ];
            DB::table('history_no')->insert($ins_data);
            echo rtrim($str,' ')."\n";
            self::upNo();
        }
    }


}
