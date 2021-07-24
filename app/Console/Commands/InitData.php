<?php

namespace App\Console\Commands;

use App\Models\LunarCalendar;
use App\Models\MongoDB;
use App\Mongodb\DaySign;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InitData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:data';

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
        $time = '2021-07-22 23:30:00';
        $txTime = strtoupper(base_convert(strtotime($time), 10, 16));
        echo $txTime."\n";
        $at =  base_convert('60FBF9B8', 16, 10) ;
        echo "60FBF9B8 ==> {$at} \n";
        echo date("Y-m-d H:i:s",$at)."\n";
        //$this->initUser();
        return true;
    }

    public function initUser()
    {
        for ($i = 0; $i < 10; $i++) {
            $avatar = [
                'https://images.jobslee.top/storage/images/header/user02.jpg',
                'https://images.jobslee.top/storage/images/header/user.jpg',
                'https://images.jobslee.top/storage/images/header/header6.jpeg',
                'https://images.jobslee.top/storage/images/header/header5.jpeg',
                'https://images.jobslee.top/storage/images/header/header4.jpeg',
                'https://images.jobslee.top/storage/images/header/head33.jpeg',
                'https://images.jobslee.top/storage/images/header/head3.jpeg',
                'https://images.jobslee.top/storage/images/header/head2.jpeg',
                'https://images.jobslee.top/storage/images/header/head22.jpeg',
                'https://images.jobslee.top/storage/images/header/head1.jpeg',

            ];
            $content = [
                '以前不离不弃的叫夫妻，现在不离不弃的是手机，一机在手，天长地久！机不在手，魂都没有。',
                '有什么别有病，没什么别没钱，缺什么也别缺健康，健康不是一切，但是没有健康就没有一切。',
                '最佳的报复不是仇恨，而是打心底发出的冷淡，干嘛花力气去恨一个不相干的人。',
                '该和善的时候一定要和善，该骂的时候千万别忍让，时时处处的彬彬有礼那是烂好人。',
                '很多的烦恼源于不够狠心，做什么都要顾及别人的感受，你总顾及别人，那谁来顾及你。',
                '错把放纵当潇洒，把颓废当自由，把逃避责任当作追求自我价值。不过是懒，怕吃苦，哪来那么多好听的理由。',
                '懒惰是索价极高的奢侈品，一旦到期清付，必定偿还不起。',
                '还是先想着如何使自己变得更优秀吧，别整天奢望会遇见什么对的人，你还太年轻，就算遇见了也抓不住！',
                '为了自己想过的生活，就要勇于放弃一些东西。这个世界没有绝对公正之处，你也永远得不到两全之计。',
                '心情不好的时候，做个深呼吸，告诉自己，不过是糟糕的一天而已，又不是糟糕一辈子。',
                '劳累了，听听音乐；伤心了，侃侃心情；失败了，从头再来；生活就该这么过，扫尽阴霾快乐自然来。',
                '别总因为迁就别人就委屈自己，这个世界没几个人值得你总弯腰，弯腰的时间久了，只会让人习惯于你的低姿态。',
                '不要活在别人眼中，更不要活在别人嘴中。世界不会因为你的抱怨不满而为你改变，你能做到的只有改变你自己！',
                '不是所有的坚持都会有结果，但是总有一些坚持，能从一寸的冰封的土地里，培育出十万朵怒放的蔷薇。'
            ];
            $data = [
                'user_name' => 'test_' . $i,
                'user_nickname' => '哎呦，不错哦' . $i,
                'user_avatar' => $avatar[$i],
                'password' => bcrypt('123456'),
                'remember_token' => '',
                'user_signature' => $content[$i],
                'email' => 'test_' . $i . '@liy.com'
            ];
            DB::table(User::tableName)->insert($data);
        }
    }

    public function initDaySign()
    {
        $week_arr = ["日", "一", "二", "三", "四", "五", "六"];
        $content = [
            '以前不离不弃的叫夫妻，现在不离不弃的是手机，一机在手，天长地久！机不在手，魂都没有。',
            '有什么别有病，没什么别没钱，缺什么也别缺健康，健康不是一切，但是没有健康就没有一切。',
            '最佳的报复不是仇恨，而是打心底发出的冷淡，干嘛花力气去恨一个不相干的人。',
            '该和善的时候一定要和善，该骂的时候千万别忍让，时时处处的彬彬有礼那是烂好人。',
            '很多的烦恼源于不够狠心，做什么都要顾及别人的感受，你总顾及别人，那谁来顾及你。',
            '错把放纵当潇洒，把颓废当自由，把逃避责任当作追求自我价值。不过是懒，怕吃苦，哪来那么多好听的理由。',
            '懒惰是索价极高的奢侈品，一旦到期清付，必定偿还不起。',
            '还是先想着如何使自己变得更优秀吧，别整天奢望会遇见什么对的人，你还太年轻，就算遇见了也抓不住！',
            '为了自己想过的生活，就要勇于放弃一些东西。这个世界没有绝对公正之处，你也永远得不到两全之计。',
            '心情不好的时候，做个深呼吸，告诉自己，不过是糟糕的一天而已，又不是糟糕一辈子。',
            '劳累了，听听音乐；伤心了，侃侃心情；失败了，从头再来；生活就该这么过，扫尽阴霾快乐自然来。',
            '别总因为迁就别人就委屈自己，这个世界没几个人值得你总弯腰，弯腰的时间久了，只会让人习惯于你的低姿态。',
            '不要活在别人眼中，更不要活在别人嘴中。世界不会因为你的抱怨不满而为你改变，你能做到的只有改变你自己！',
            '不是所有的坚持都会有结果，但是总有一些坚持，能从一寸的冰封的土地里，培育出十万朵怒放的蔷薇。'
        ];
        $date = '2021-07';
        for ($i = 1; $i <= 31; $i++) {
            $day = $i;
            if ($i < 10) {
                $day = '0' . $i;
            }
            $at = $date . '-' . $day;
            echo $at . "\n";
            $lunar = LunarCalendar::solarToLunar('2021', '07', $i);
            $week = date('w', strtotime($at));
            $created_at = $at . " 08:00:00";
            $idx = $i - 1;
            if ($idx > 13) {
                $idx = mt_rand(0, 13);
            }
            $data = [
                'id' => intval(MongoDB::getTableIdx(DaySign::tableName, true)),
                'user_id' => 1,
                'user_name' => '一切都刚刚好 ‘',
                'user_avatar' => 'https://images.jobslee.top/storage/images/header/mao.jpg',
                'views' => mt_rand(354, 9871),
                'lunar_year' => $lunar[3],
                'cn_year' => $lunar[0],
                'cn_month' => $lunar[1],
                'cn_day' => $lunar[2],
                'bron_year' => $lunar[4],
                'week' => $week_arr[$week],
                'content' => $content[$idx],
                'images' => [
                    'https://images.jobslee.top/storage/images/ysrj/banner1.jpg',
                    'https://images.jobslee.top/storage/images/ysrj/banner2.jpg',
                    'https://images.jobslee.top/storage/images/ysrj/banner3.jpg'
                ],
                'is_delete' => 2,
                'created_at' => $created_at,
                'updated_at' => $created_at
            ];

            DaySign::create($data);


        }
    }
}
