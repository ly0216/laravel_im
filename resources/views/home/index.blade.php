<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{asset('static/layui/css/layui.css')}}">
    <link rel="stylesheet" href="{{asset('static/css/index.css')}}">
    <!-- Javascript -->
    <script type="text/javascript" src="{{asset('static/js/jquery.3.2.1.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('static/layui/layui.js')}}"></script>
    <script type="text/javascript" src="{{asset('static/js/index.js')}}"></script>
</head>

<body>
<div class="user_info">
    <div class="user_avatar">
        <img src="https://gimg2.baidu.com/image_search/src=http%3A%2F%2Fc-ssl.duitang.com%2Fuploads%2Fitem%2F202006%2F01%2F20200601140050_qnooe.thumb.1000_0.jpeg&refer=http%3A%2F%2Fc-ssl.duitang.com&app=2002&size=f9999,10000&q=a80&n=0&g=0n&fmt=jpeg?sec=1629112996&t=fb9630cd84c2fc0106ce86ba911f4ba9">
    </div>
    <div class="user_name">
        暖暖'
    </div>
    <div class="user_signature">
        &nbsp;&nbsp;&nbsp;&nbsp;我们两支汤匙一个碗 ，左心房暖暖的好饱满。我们两支汤匙一个碗 ，左心房暖暖的好饱满。我们两支汤匙一个碗 ，左心房暖暖的好饱满。
    </div>
</div>
<div class="div_label">
    <div class="day_tag">日签</div>
    <div class="clock">打卡</div>
    <div class="words">文字</div>
    <div class="chat">聊天</div>
</div>

</body>

</html>
