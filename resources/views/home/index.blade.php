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
    <script type="text/javascript" src="{{asset('static/js/index.js')}}"></script>
</head>

<body>
<div class="user_info">
    <div class="user_avatar">
        <img id="user_avatar" src="https://images.jobslee.top/storage/images/header/header4.jpeg">
    </div>
    <div class="user_name">
        加载中。。。
    </div>
    <div class="user_signature">
        加载中。。。
    </div>
</div>
<div class="div_label">
    <div class="day_tag">日签</div>
    <div class="clock">打卡</div>
    <div class="words">文字</div>
    <div class="chat">聊天</div>
</div>

<div class="text_label_day">心情日签<span class="label_more">more</span></div>
<div class="day_tag_list">
    <div class="table_left content_text">

        <div class="table_user_avatar">
            <img src="https://images.jobslee.top/storage/images/header/header5.jpeg">
        </div>
        <div class="table_user_name">武媚娘</div>
    </div>
    <div class="table_center content_text">
        <div class="table_user_avatar">
            <img src="https://images.jobslee.top/storage/images/header/header6.jpeg">
        </div>
        <div class="table_user_name">西施</div>
    </div>
    <div class="table_right content_text">
        <div class="table_user_avatar">
            <img src="https://images.jobslee.top/storage/images/header/user02.jpg">
        </div>
        <div class="table_user_name">貂蝉</div>
    </div>
</div>


</body>
<script>

</script>

</html>
