<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>好友列表</title>

    <!-- Fonts -->
    {{--<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">--}}

    <!-- Styles -->
    <link rel="stylesheet" href="{{asset('static/layui/css/layui.css')}}">
    <link rel="stylesheet" href="{{asset('static/css/friend.css')}}?v={{$version}}">
    <!-- Javascript -->
    <script type="text/javascript" src="{{asset('static/layui/layui.js')}}"></script>
    <script type="text/javascript" src="{{asset('static/js/jquery.3.2.1.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('static/js/config.js')}}?v={{$version}}"></script>
    <script type="text/javascript" src="{{asset('static/js/friend.js')}}?v={{$version}}"></script>
</head>

<body>
<div class="layui-container">
    <div class="layui-row">
        <div class="layui-col-xs1">
            <div class="chat_title click_back">🔙</div>
        </div>
        <div class="layui-col-xs10">
            <div class="chat_title party_title_text">好友列表</div>
        </div>
        <div class="layui-col-xs1">
            <div class="chat_title is_collect"></div>
        </div>
    </div>
    <div class="layui-row layui-col-space15 content" id="flow_list_div">

       {{-- <div class="layui-row friend_item">
            <div class="layui-col-xs2">
                <div class="user_avatar"><img src="https://images.jobslee.top/storage/images/header/header4.jpeg"/></div>
            </div>
            <div class="layui-col-xs8">
                <div class="item_content">
                    <div class="user_nickname">昵称或者备注</div>
                    <div class="last_message">说个啥好呢</div>
                </div>
            </div>
            <div class="layui-col-xs2">
                <div class="message_tip">
                    <div class="last_time">
                        <span class="layui-badge layui-bg-gray">很久以前</span>
                    </div>
                    <div class="unread_number">
                        <span class="layui-badge layui-bg-red">10</span>
                    </div>
                </div>
            </div>
        </div>
--}}

    </div>
</div>
</body>
</html>
