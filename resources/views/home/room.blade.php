<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>聊天会话</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{asset('static/layui/css/layui.css')}}">
    <link rel="stylesheet" href="{{asset('static/css/room.css')}}">
    <!-- Javascript -->
    <script type="text/javascript" src="{{asset('static/layui/layui.js')}}"></script>
    <script type="text/javascript" src="{{asset('static/js/jquery.3.2.1.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('static/js/room.js')}}"></script>

</head>

<body>
<input type="hidden" id="chat_sn" value="{{$chat_sn}}">
<div class="layui-container" style="padding: 0;">
    <div class="layui-row">
        <div class="layui-col-xs1">
            <div class="chat_title">🔙</div>
        </div>
        <div class="layui-col-xs10">
            <div class="chat_title">💬测试聊天会话</div>
        </div>
        <div class="layui-col-xs1">
            <div class="chat_title"></div>
        </div>
    </div>
    <div class="content" id="content_div">
        <div class="message">
            <div class="layui-row message_content system_message">
                <div class="layui-col-xs1">&nbsp;</div>
                <div class="layui-col-xs10">
                    <div>⚜️欢迎来到测试群聊⚜️</div>
                </div>
                <div class="layui-col-xs1">&nbsp;</div>
            </div>

        </div>
        <div class="bottom_content"></div>
    </div>
    <div class="layui-row float_bottom">
        <div class="layui-col-xs10">
            <div class="bottom_text">
                <input type="text" id="content_message" value="">
            </div>
        </div>
        <div class="layui-col-xs2">
            <div class="bottom_button" id="send_message">🔜</div>
        </div>

    </div>

</div>
</body>

</html>
