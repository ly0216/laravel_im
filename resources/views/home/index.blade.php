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
<div class="layui-container">
    <div class="layui-row">
        <div class="layui-col-xs4 user_avatar">
            <img id="user_avatar" src="https://images.jobslee.top/storage/images/header/header4.jpeg">
        </div>
        <div class="layui-col-xs8">
            <div class="layui-row user_name">name</div>
            <div class="layui-row user_signature">signature</div>
        </div>
    </div>

    <div class="layui-row layui-col-space10">
        <div class="layui-col-xs4 ">
            <div class="day_tag">派对列表</div>
        </div>
        <div class="layui-col-xs4 ">
            <div class="words">我的派对</div>
        </div>
        <div class="layui-col-xs4 rand-party">
            <div class="chat">随机匹配</div>
        </div>
    </div>
    <div class="layui-row">
        <div class="layui-col-xs12 text_label_day">
            🎉开心派对
        </div>
    </div>
    <div class="layui-row layui-col-space15">
        <div class="layui-col-xs4 ">
            <div class="label_content">
                <div class="content_image">
                    <img src="https://images.jobslee.top/storage/images/ysrj/cls03.png">
                </div>
                <div class="content_text">派对名称</div>
            </div>

        </div>
        <div class="layui-col-xs4">
            <div class="label_content">
                <div class="content_image">
                    <img src="https://images.jobslee.top/storage/images/ysrj/cls03.png">
                </div>
                <div class="content_text">派对名称，派对名称</div>
            </div>
        </div>
        <div class="layui-col-xs4">
            <div class="label_content">
                <div class="content_image">
                    <img src="https://images.jobslee.top/storage/images/ysrj/cls03.png">
                </div>
                <div class="content_text">派对名称，派对名称</div>
            </div>
        </div>

    </div>
    <div class="layui-row">
        <div class="layui-col-xs12 text_label_day">
            📆心情日签
        </div>
    </div>
    <div class="layui-row layui-col-space15">
        <div class="layui-col-xs4 ">
            <div class="label_content">
                <div class="content_image">
                    <img src="https://images.jobslee.top/storage/images/ysrj/user02.jpg">
                </div>
            </div>
            <div class="label_user_avatar">
                <img src="https://images.jobslee.top/storage/images/header/header5.jpeg">
            </div>
            <div class="label_user_name">武媚娘</div>
        </div>
        <div class="layui-col-xs4">
            <div class="label_content">
                <div class="content_image">
                    <img src="https://images.jobslee.top/storage/images/ysrj/user02.jpg">
                </div>
            </div>
            <div class="label_user_avatar">
                <img src="https://images.jobslee.top/storage/images/header/header5.jpeg">
            </div>
            <div class="label_user_name">武媚娘</div>
        </div>
        <div class="layui-col-xs4">
            <div class="label_content">
                <div class="content_image">
                    <img src="https://images.jobslee.top/storage/images/ysrj/user02.jpg">
                </div>
            </div>
            <div class="label_user_avatar">
                <img src="https://images.jobslee.top/storage/images/header/header5.jpeg">
            </div>
            <div class="label_user_name">武媚娘</div>
        </div>

    </div>
    <div class="layui-row">
        <div class="layui-col-xs12 text_label_day">
            📸打卡日常
        </div>
    </div>
    <div class="layui-row layui-col-space15">
        <div class="layui-col-xs4">
            <div class="label_content">
                <div class="content_image">
                    <img src="https://images.jobslee.top/storage/images/ysrj/cls03.png">
                </div>
                <div class="content_text" style="line-height: 70px">要快乐哦，哈哈哈哈哈哈</div>
            </div>
            <div class="label_user_avatar">
                <img src="https://images.jobslee.top/storage/images/header/header5.jpeg">
            </div>
        </div>
        <div class="layui-col-xs4">
            <div class="label_content">
                <div class="content_image">
                    <img src="https://images.jobslee.top/storage/images/ysrj/user02.jpg">
                </div>
                <div class="content_text" style="line-height: 70px">要快乐哦，哈哈哈哈哈哈</div>
            </div>
            <div class="label_user_avatar">
                <img src="https://images.jobslee.top/storage/images/header/header5.jpeg">
            </div>
        </div>
        <div class="layui-col-xs4">
            <div class="label_content">
                <div class="content_image">
                    <img src="https://images.jobslee.top/storage/images/ysrj/cls04.png">
                </div>
                <div class="content_text" style="line-height: 70px">要快乐哦，哈哈哈哈哈哈</div>
            </div>
            <div class="label_user_avatar">
                <img src="https://images.jobslee.top/storage/images/header/header5.jpeg">
            </div>
        </div>
    </div>
</div>
</body>
</html>
