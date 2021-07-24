<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>洋式日迹</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{asset('static/layui/css/layui.css')}}">
    <link rel="stylesheet" href="{{asset('static/css/index.css')}}">
    <!-- Javascript -->
    <script type="text/javascript" src="{{asset('static/layui/layui.js')}}"></script>
    <script type="text/javascript" src="{{asset('static/js/jquery.3.2.1.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('static/js/config.js')}}"></script>
    <script type="text/javascript" src="{{asset('static/js/index.js')}}"></script>
</head>

<body>
<div class="layui-container">
    <div class="layui-row">
        <div class="layui-col-xs4 user_avatar">
            <img id="user_avatar" src="https://images.jobslee.top/storage/images/header/mao.jpg">
        </div>
        <div class="layui-col-xs8">
            <div class="layui-row user_name">name</div>
            <div class="layui-row user_signature">signature</div>
        </div>
    </div>

    <div class="layui-row layui-col-space10">
        <div class="layui-col-xs4" id="create_party">
            <div class="day_tag">创建派对</div>
        </div>
        <div class="layui-col-xs4" id="party_list">
            <div class="words">派对列表</div>
        </div>
        <div class="layui-col-xs4" id="rand_party">
            <div class="chat">随机匹配</div>
        </div>
    </div>
    <div class="layui-row">
        <div class="layui-col-xs12 text_label_day">
            热门派对
        </div>
    </div>
    <div class="layui-row layui-col-space15" id="flow_list_div">

        {{--<div class="layui-col-xs6 flow_item">
            <div class="label_content">
                <div class="content_body">
                    <div class="content_body_title">要快乐哦，哈哈哈哈哈哈</div>
                    <div class="content_body_text">
                        以前不离不弃的叫夫妻，现在不离不弃的是手机，一机在手，天长地久！机不在手，魂都没有。以前不离不弃的叫夫妻，现在不离不弃的是手机，一机在手，天长地久！机不在手，魂都没有。
                    </div>
                </div>
                <div class="content_text">一切都刚刚好'</div>
            </div>
            <div class="label_user_avatar">
                <img src="https://images.jobslee.top/storage/images/header/header5.jpeg" lay-src="https://images.jobslee.top/storage/images/header/header5.jpeg">
            </div>
        </div>--}}


    </div>
</div>
</body>
</html>
