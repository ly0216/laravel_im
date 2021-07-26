<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>洋式日迹</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="{{asset('static/css/common.css')}}?family=Nunito:wght@200;600&display=swap">
        <!-- Styles -->
        <link rel="stylesheet" href="{{asset('static/css/im.css')}}">
        <!-- Javascript -->
        <script type="text/javascript" src="{{asset('static/js/jquery.3.2.1.min.js')}}"></script>
        <script type="text/javascript" src="{{asset('static/js/config.js')}}?v={{$version}}"></script>

    </head>
    <body style="background-color: #0C0C0C;">
        <div class="flex-center position-ref full-height">
            <div class="content">
                <div class="title m-b-md">
                    LiyIM
                </div>

                <div class="links">
                    <a href="/login" id="sign_in">SIGN IN</a>
                </div>
            </div>
            <div class="version-no">
                im.jobslee.top BEAT V<span id="app-version">0.0.1</span>
            </div>
        </div>
    </body>
    <script>
        $('#app-version').text(config.APP_VERSION);
    </script>

</html>
