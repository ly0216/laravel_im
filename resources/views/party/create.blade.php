<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>洋式日迹-创建派对</title>

    <!-- Fonts -->
    {{--<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">--}}

    <!-- Styles -->
    <link rel="stylesheet" href="{{asset('static/layui/css/layui.css')}}">
    <link rel="stylesheet" href="{{asset('static/css/common.css')}}">
    <!-- Javascript -->
    <script type="text/javascript" src="{{asset('static/layui/layui.js')}}"></script>
    <script type="text/javascript" src="{{asset('static/js/jquery.3.2.1.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('static/js/config.js')}}"></script>
    <script type="text/javascript" src="{{asset('static/js/create.js')}}"></script>
</head>

<body>
<div class="layui-container">
    <div class="layui-row">
        <div class="layui-row">
            <div class="layui-col-xs1">
                <div class="chat_title click_back">🔙</div>
            </div>
            <div class="layui-col-xs10">
                <div class="chat_title party_title_text">创建派对</div>
            </div>
            <div class="layui-col-xs1">
                <div class="chat_title is_collect"></div>
            </div>
        </div>
    </div>
</div>
<div class="layui-form">
    <div class="layui-form-item ">
        <div class="layui-input-block input_margin_20">
            <input type="text" name="title" required  lay-verify="required" placeholder="请输入派对名称" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <div class="layui-input-block input_margin_20">
            <textarea name="content" placeholder="请输入派对简介" class="layui-textarea"></textarea>
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-tab layui-tab-brief input_margin_20" lay-filter="background-image">
            <ul class="layui-tab-title">
                <li  class="layui-this tab_item_li" lay-id="11">鸟瞰大地</li>
                <li class="tab_item_li" lay-id="10">眺望星空</li>
                <li class="tab_item_li" lay-id="9">面朝大海</li>
                <li class="tab_item_li" lay-id="23">小丑🤡</li>
                <li class="tab_item_li" lay-id="22">小丑女</li>
            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show">
                    <img style="width: 355px;height: 550px" src="https://images.jobslee.top/storage/images/backgroundimage/bg-11.jpeg">
                </div>
                <div class="layui-tab-item">
                    <img style="width: 355px;height: 550px" src="https://images.jobslee.top/storage/images/backgroundimage/bg-10.jpeg">
                </div>
                <div class="layui-tab-item">
                    <img style="width: 355px;height: 550px" src="https://images.jobslee.top/storage/images/backgroundimage/bg-9.jpeg">
                </div>
                <div class="layui-tab-item">
                    <img style="width: 355px;height: 550px" src="https://images.jobslee.top/storage/images/backgroundimage/bg-23.jpeg">
                </div>
                <div class="layui-tab-item">
                    <img style="width: 355px;height: 550px" src="https://images.jobslee.top/storage/images/backgroundimage/bg-22.jpg">
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="background_image" id="background_image" value="https://images.jobslee.top/storage/images/backgroundimage/bg-11.jpeg">
    <div class="layui-form-item">
        <div class="layui-input-block input_margin_20" style="text-align: center;">
            <button class="layui-btn layui-btn-black"  lay-submit lay-filter="createParty">立即提交</button>
            <button class="layui-btn layui-btn-black call_back">返回上级</button>

        </div>
    </div>
</div>

</body>
</html>
