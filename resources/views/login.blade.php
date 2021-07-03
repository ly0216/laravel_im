<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="{{asset('static/css/common.css')}}?family=Nunito:wght@200;600&display=swap">
        <!-- Styles -->
        <link rel="stylesheet" href="{{asset('static/css/im.css')}}">
        <link rel="stylesheet" href="{{asset('static/layui/css/layui.css')}}">
        <!-- Javascript -->
        <script type="text/javascript" src="{{asset('static/js/jquery.3.2.1.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('static/layui/layui.js')}}"></script>
        <script type="text/javascript" src="{{asset('static/js/login.js')}}"></script>

    </head>
    <body>
    <div style="margin: 0 auto; max-width: 1140px;">

    </div>
        <div class="flex-center position-ref top_tip">
            这是个什么玩意？
        </div>
    <div class="flex-center position-ref login-height">
        <div class="layui-form top20" style="margin-left: -50px">
            <div class="layui-form-item" >
                <label class="layui-form-label">用户名</label>
                <div class="layui-input-block div-w180">
                    <input type="text" name="user_name" required  lay-verify="required" placeholder="请输入用户名" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">密&nbsp;&nbsp;&nbsp;&nbsp;码</label>
                <div class="layui-input-block div-w180">
                    <input type="password" name="password" required lay-verify="required" placeholder="请输入密码" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit lay-filter="signIn">立即登录</button>
                </div>
            </div>

        </div>
        </div>
    </body>

</html>
