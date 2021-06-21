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
    <link rel="stylesheet" href="{{asset('static/css/im.css')}}">

    <script type="text/javascript" src="{{asset('static/js/jquery.3.2.1.min.js')}}"></script>
</head>
<body>
<div class="flex-center position-ref full-height">

    <div class="content">
        <div class="title m-b-md">
            Hello Im
        </div>

        <div class="links">
            <a href="javascript:;" id="check_token_msg">checkToken</a>
            <a href="javascript:;" id="send_data_msg">sendData</a>
        </div>
    </div>
</div>
</body>
<script>
    var wsServer = 'ws://liy.im.com/ws';
    var websocket = new WebSocket(wsServer);
    websocket.onopen = function (evt) {
        console.log("Connected to WebSocket server.");
    };

    websocket.onclose = function (evt) {
        console.log("Disconnected");
    };

    websocket.onmessage = function (evt) {
        console.log('Retrieved data from server: ' + evt.data);
        let data = JSON.parse(evt.data) ;
        if(data.code == 0){
            if(data.data.type == 'connection'){
                let data = {
                    'action': 'checkToken',
                    'user_id':'13408',
                    'token': 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9saXkuaW0uY29tXC9hcGlcL2xvZ2luIiwiaWF0IjoxNjIzOTE2MTY2LCJleHAiOjE2MjQwMDI1NjYsIm5iZiI6MTYyMzkxNjE2NiwianRpIjoiMTRzaHd6aWpGZVpKN254cSIsInN1YiI6MTM0ODMsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.Hkx4bnkch253Yx6yx1qU1E14Qn4bXlRDWv92VWJdq0Q'
                };
                websocket.send(JSON.stringify(data));
            }
        }
    };

    websocket.onerror = function (evt, e) {
        console.log('Error occured: ' + evt.data);
    };

    $('#check_token_msg').on('click', function () {
        let data = {
            'action': 'checkToken',
            'user_id':'13408',
            'token': 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9saXkuaW0uY29tXC9hcGlcL2xvZ2luIiwiaWF0IjoxNjIzOTE2MTY2LCJleHAiOjE2MjQwMDI1NjYsIm5iZiI6MTYyMzkxNjE2NiwianRpIjoiMTRzaHd6aWpGZVpKN254cSIsInN1YiI6MTM0ODMsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.Hkx4bnkch253Yx6yx1qU1E14Qn4bXlRDWv92VWJdq0Q'
        };
        websocket.send(JSON.stringify(data));
    });

    $('#send_data_msg').on('click', function () {
        let data = {
            'action':'sendMessage',
            'user_id':'13408',
            'message_type':'text',
            'content':'what are you 弄啥类？'
        };
        websocket.send(JSON.stringify(data));
    });


</script>
</html>
