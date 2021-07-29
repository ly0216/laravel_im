<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>好友申请列表</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{asset('static/layui/css/layui.css')}}">
    <link rel="stylesheet" href="{{asset('static/css/apply.css')}}?v={{$version}}">
    <!-- Javascript -->
    <script type="text/javascript" src="{{asset('static/layui/layui.js')}}"></script>
    <script type="text/javascript" src="{{asset('static/js/jquery.3.2.1.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('static/js/config.js')}}?v={{$version}}"></script>
    <script type="text/javascript" src="{{asset('static/js/apply.js')}}?v={{$version}}"></script>
</head>

<body>
<div class="layui-container">
    <div class="layui-row">
        <div class="layui-row">
            <div class="layui-col-xs1">
                <div class="chat_title click_back">🔙</div>
            </div>
            <div class="layui-col-xs10">
                <div class="chat_title party_title_text">好友申请列表</div>
            </div>
            <div class="layui-col-xs1">
                <div class="chat_title is_collect"></div>
            </div>
        </div>
    </div>
    <div class="layui-row layui-col-space15" id="flow_list_div">
        {{--<div class="layui-row flow_item">
            <div class="layui-col-xs3">
                <div class="user_avatar"><img src="https://images.jobslee.top/storage/images/header/header5.jpeg"></div>
            </div>
            <div class="layui-col-xs7">
                <div class="layui-row user_nickname">糊糊</div>
                <div class="layui-row data_time">2021-12-12 12:21:45</div>
            </div>
            <div class="layui-col-xs2">
                <div class="layui-row agree_btn" apply_id="">同意</div>
                <div class="layui-row refuse_btn" apply_id="">拒绝</div>
            </div>
        </div>--}}


    </div>
</div>
</body>
</html>
