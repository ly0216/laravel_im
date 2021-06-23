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
<style>
    pre {outline: 1px solid #ccc; padding: 5px; margin: 5px; }
    .string { color: green; }
    .number { color: darkorange; }
    .boolean { color: blue; }
    .null { color: magenta; }
    .key { color: red; }
</style>
<body>

<div class="message-list" style="float: left;margin-left: 50px;margin-top: 20px;width: 75%;">

</div>
<div class="links" style="float: right;margin-top:50px;margin-right: 50px">
    <a href="javascript:;" id="check_token_msg">checkToken</a>
    <a href="javascript:;" id="send_data_msg">sendData</a>
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

        let data = JSON.parse(evt.data) ;
        $('.message-list').append('<pre>'+syntaxHighlight(data)+'</pre>');
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

    function syntaxHighlight(json) {
        if (typeof json != 'string') {
            json = JSON.stringify(json, undefined, 2);
        }
        json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function(match) {
            var cls = 'number';
            if (/^"/.test(match)) {
                if (/:$/.test(match)) {
                    cls = 'key';
                } else {
                    cls = 'string';
                }
            } else if (/true|false/.test(match)) {
                cls = 'boolean';
            } else if (/null/.test(match)) {
                cls = 'null';
            }
            return '<span class="' + cls + '">' + match + '</span>';
        });
    }

</script>
</html>
