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
        <!-- Javascript -->
        <script type="text/javascript" src="{{asset('static/js/jquery.3.2.1.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('static/js/login.js')}}"></script>

    </head>
    <body>
        <div class="flex-center position-ref top_tip">
            这是个什么玩意？
        </div>
        <div class="flex-center position-ref full-height">
            <div class="content">
                <div class="title m-b-md">
                    LiyIM
                </div>

                <div class="links">
                    <a href="/login" id="sign_in">SIGN IN</a>
                </div>
            </div>
        </div>
    </body>

</html>
