<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>个人信息</title>

    <!-- Fonts -->
    {{--<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">--}}

    <!-- Styles -->
    <link rel="stylesheet" href="{{asset('static/layui/css/layui.css')}}">
    <link rel="stylesheet" href="{{asset('static/css/my.css')}}?v={{$version}}">
    <!-- Javascript -->
    <script type="text/javascript" src="{{asset('static/layui/layui.js')}}"></script>
    <script type="text/javascript" src="{{asset('static/js/jquery.3.2.1.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('static/js/config.js')}}?v={{$version}}"></script>
    <script type="text/javascript" src="{{asset('static/js/my.js')}}?v={{$version}}"></script>
</head>

<body>
<div class="layui-container">
    <div class="layui-row">
        <div class="layui-row">
            <div class="layui-col-xs1">
                <div class="chat_title click_back">🔙</div>
            </div>
            <div class="layui-col-xs10">
                <div class="chat_title party_title_text">个人信息</div>
            </div>
            <div class="layui-col-xs1">
                <div class="chat_title is_collect"></div>
            </div>
        </div>
    </div>
    <div class="layui-row layui-col-space15" id="flow_list_div">
        <div class="layui-row flow_item">
            <div class="layui-col-xs3">
                <div class="user_avatar"><img id="user_avatar" src="https://images.jobslee.top/storage/images/header/header5.jpeg"></div>
                <input type="hidden" name="user_avatar" id="user_info_avatar" value="">
            </div>
            <div class="layui-col-xs7">
                <div class="layui-row avatar_number">
                    <span class="layui-icon layui-icon-male number_n">0</span> <span>/</span> <span class="layui-icon layui-icon-female number_m">7</span>
                </div>
                <div class="layui-row avatar_tip">
                    {{--<span class="layui-icon layui-icon-tips"></span>头像每个月可上传<span class="tip">7</span>次，且每天只能上传<span class="tip">1</span>次。--}}
                    <span class="layui-icon layui-icon-tips">自定义头像上传还没有开放哦，快去头像库看看吧！</span>
                </div>
            </div>
            <div class="layui-col-xs2">
                <div class="layui-row upload_btn"><span class="layui-icon layui-icon-carousel show_avatar_list"></span></div>
            </div>
        </div>

        <div class="layui-row layui-col-space15" id="avatar_list_div" style="display: none">
            {{--<div class="layui-col-xs4 avatar_item" avatar_id="1">
                <div class="layui-col-xs4 avatar_item_val">
                    <img src="https://images.jobslee.top/storage/images/header/avatar1.jpg">
                </div>
            </div>--}}
        </div>


        <div class="layui-row flow_item">
            <div class="layui-col-xs2 input_tip">
                昵称
            </div>
            <div class="layui-col-xs10 input_text">
                <input type="text" id="user_nickname" name="user_name" class="input_user_name" value="" placeholder="填写您的昵称">
            </div>
        </div>
        <div class="layui-row flow_item">
            <div class="layui-col-xs2 input_tip">
                签名
            </div>
            <div class="layui-col-xs10 input_textarea">
                <textarea rows="3" id="user_sign" cols="40" placeholder="请填写您的签名"></textarea>
            </div>
        </div>
        <div class="layui-row flow_item">
            <div class="layui-col-xs12">
                <div class="sub_user_base" >提&nbsp;&nbsp;&nbsp;&nbsp;交</div>
            </div>
        </div>

        <div class="layui-row flow_item">

            <div class="layui-col-xs12">
                <div class="change_password_btn">我要修改密码</div>
            </div>
        </div>
        <div class="layui-row flow_item change_password" style="display: none">
            <div class="layui-col-xs2 input_tip">
                旧密码
            </div>
            <div class="layui-col-xs10 input_text">
                <input type="text" id="old_password" name="old_password" class="input_user_name" value="" placeholder="填写您的昵称">
            </div>
        </div>
        <div class="layui-row flow_item change_password" style="display: none">
            <div class="layui-col-xs2 input_tip">
                新密码
            </div>
            <div class="layui-col-xs10 input_text">
                <input type="text" id="new_password" name="new_password" class="input_user_name" value="" placeholder="填写您的昵称">
            </div>
        </div>
        <div class="layui-row flow_item change_password" style="display: none">
            <div class="layui-col-xs2 input_tip">
                确认下
            </div>
            <div class="layui-col-xs10 input_text">
                <input type="text" id="rep_password" name="rep_password" class="input_user_name" value="" placeholder="填写您的昵称">
            </div>
        </div>
        <div class="layui-row flow_item change_password" style="display: none">

            <div class="layui-col-xs12">
                <div class="sub_change_password">修改密码</div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
