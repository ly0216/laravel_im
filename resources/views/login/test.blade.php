<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel</title>

    <!-- Fonts -->
    <link href="{{asset('static/css/common.css')}}" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{asset('static/css/im.css')}}">

    <script type="text/javascript" src="{{asset('static/js/jquery.3.2.1.min.js')}}"></script>
</head>
<style>
    pre {
        outline: 1px solid #ccc;
        padding: 5px;
        margin: 5px;
    }

    .string {
        color: green;
    }

    .number {
        color: darkorange;
    }

    .boolean {
        color: blue;
    }

    .null {
        color: magenta;
    }

    .key {
        color: red;
    }
</style>
<body>

<div class="message-list" style="float: left;margin-left: 50px;margin-top: 20px;width: 75%;">

</div>
<div class="links" style="float: right;margin-top:50px;margin-right: 50px">
    <a href="javascript:;"><h1><span id="show_message_number">0</span></h1></a>
    <br>
    <a href="javascript:;" id="check_token_msg"><h1>checkToken</h1></a>
    <br>
    <a href="javascript:;" id="send_data_msg"><h1>sendData</h1></a>
</div>
<input type="hidden" id="user_id" value="{{$user_id}}">
<input type="hidden" id="message_number" value="0">
</body>
<script>
    let user_id = $("#user_id").val();
    let wsServer = 'ws://liy.im.com/ws';
    let websocket = new WebSocket(wsServer);
    let head_check_number = 0;
    let check_total_number = 100;//心跳检测次数
    let timeOut = 30000;//心跳检测间隔ms

    function reOpen() {
        websocket.onopen = function (evt) {
            console.log("Connected to WebSocket server.");

        };
    }

    if (user_id) {
        console.log(user_id);
        websocket.onopen = function (evt) {
            console.log("Connected to WebSocket server.");
            console.log('head check begin');
            headCheck(websocket, user_id)
        };

        websocket.onclose = function (evt) {
            console.log("Disconnected");
            reOpen();
        };


        websocket.onmessage = function (evt) {

            let data = JSON.parse(evt.data);
            $('.message-list').prepend('<pre>' + syntaxHighlight(data) + '</pre>');
            let number = $('#message_number').val();
            number++;
            $('#message_number').val(number);
            $('#show_message_number').text(number);
            if (data.code == 0) {
                if (data.data.type == 'connection') {
                    let data = {
                        'action': 'checkToken',
                        'user_id': user_id,
                        'token': 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9saXkuaW0uY29tXC9hcGlcL2xvZ2luIiwiaWF0IjoxNjIzOTE2MTY2LCJleHAiOjE2MjQwMDI1NjYsIm5iZiI6MTYyMzkxNjE2NiwianRpIjoiMTRzaHd6aWpGZVpKN254cSIsInN1YiI6MTM0ODMsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.Hkx4bnkch253Yx6yx1qU1E14Qn4bXlRDWv92VWJdq0Q'
                    };
                    websocket.send(JSON.stringify(data));
                }
            }
        };

        websocket.onerror = function (evt, e) {
            console.log('Error occured: ' + evt.data);
        };
    }

    function headCheck(ws, user_id) {

        setTimeout(function () {
            setCheck(ws, user_id);
        }, 2000); //这里设置心跳间隔(ms)

    }

    function setCheck(ws, user_id) {
        console.log('head check init');
        let data = {
            'action': 'headCheck',
            'user_id': user_id,
        };
        let interVal = setInterval(() => {
            head_check_number++;
            if (head_check_number <= check_total_number) {
                if (ws.readyState === 1) {
                    console.log('head check send [' + head_check_number + ']');
                    ws.send(JSON.stringify(data));
                }

            } else {
                clearInterval(interVal);
                head_check_number = 0;
            }
        }, timeOut);
    }

    $('#check_token_msg').on('click', function () {
        let data = {
            'action': 'checkToken',
            'user_id': '13408',
            'token': 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9saXkuaW0uY29tXC9hcGlcL2xvZ2luIiwiaWF0IjoxNjIzOTE2MTY2LCJleHAiOjE2MjQwMDI1NjYsIm5iZiI6MTYyMzkxNjE2NiwianRpIjoiMTRzaHd6aWpGZVpKN254cSIsInN1YiI6MTM0ODMsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.Hkx4bnkch253Yx6yx1qU1E14Qn4bXlRDWv92VWJdq0Q'
        };
        websocket.send(JSON.stringify(data));
    });

    $('#send_data_msg').on('click', function () {
        let data = {
            'action': 'sendMessage',
            'user_id': '13408',
            'message_type': 'text',
            'content': 'what are you 弄啥类？'
        };
        websocket.send(JSON.stringify(data));
    });

    function syntaxHighlight(json) {
        if (typeof json != 'string') {
            json = JSON.stringify(json, undefined, 2);
        }
        json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
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
