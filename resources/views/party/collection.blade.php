<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>我的收藏</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{asset('static/layui/css/layui.css')}}">
    <link rel="stylesheet" href="{{asset('static/css/collection.css')}}?v={{$version}}">
    <!-- Javascript -->
    <script type="text/javascript" src="{{asset('static/layui/layui.js')}}"></script>
    <script type="text/javascript" src="{{asset('static/js/jquery.3.2.1.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('static/js/config.js')}}?v={{$version}}"></script>
    <script type="text/javascript" src="{{asset('static/js/collection.js')}}?v={{$version}}"></script>
</head>

<body>
<div class="layui-container">
    <div class="layui-row">
        <div class="layui-row">
            <div class="layui-col-xs1">
                <div class="chat_title click_back">🔙</div>
            </div>
            <div class="layui-col-xs10">
                <div class="chat_title party_title_text">收藏列表</div>
            </div>
            <div class="layui-col-xs1">
                <div class="chat_title is_collect"></div>
            </div>
        </div>
    </div>
    <div class="layui-row layui-col-space15 content" id="flow_list_div">
        {{--<div class="layui-col-xs6 flow_item">
            <div class="party_leader_avatar">
                <img src="https://images.jobslee.top/storage/images/header/header4.jpeg">
            </div>
            <div class="party_content">
                <div class="party_title">面朝大海，春暖花开</div>
                <div class="party_remark">这是派对的介绍这是派对的介绍这是派对的介绍这是派对的介绍这是派对的介绍</div>
                <div class="party_bottom">
                    <div class="create_at">2021-07-29 11:34:21</div>
                    <div class="un_collection">删除</div>
                </div>
            </div>

        </div>--}}


    </div>
</div>
</body>
</html>
