<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>洋式日迹</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="{{asset('static/css/common.css')}}?family=Nunito:wght@200;600&display=swap">
    <!-- Styles -->
    <link rel="stylesheet" href="{{asset('static/css/im.css')}}?v={{$version}}">
    <link rel="stylesheet" href="{{asset('static/layui/css/layui.css')}}">
    <!-- Javascript -->
    <script type="text/javascript" src="{{asset('static/js/jquery.3.2.1.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('static/layui/layui.js')}}"></script>
    <script type="text/javascript" src="{{asset('static/js/config.js')}}?v={{$version}}"></script>
    <script type="text/javascript" src="{{asset('static/js/login.js')}}?v={{$version}}"></script>

</head>
<body style="background-color: #0C0C0C;">
<div style="margin: 0 auto; max-width: 1140px;">

</div>

<div class="flex-center position-ref login-height">
    <div class="layui-form top20" style="margin-left: -50px">
        <div class="layui-form-item">
            <label class="layui-form-label login-label" >UserName</label>
            <div class="layui-input-block div-w180">
                <input type="text" name="user_name" required lay-verify="required" placeholder="Enter one user name" autocomplete="off" class="layui-input login-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label login-label" >PassWord</label>
            <div class="layui-input-block div-w180">
                <input type="password" name="password" required lay-verify="required" placeholder="Please input a password" autocomplete="off" class="layui-input login-input">
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn im-login-btn"  lay-submit lay-filter="signIn">Sign IN</button>
            </div>
        </div>

    </div>
</div>
</body>

</html>
