$(document).ready(function(){

    layui.use('form', function() {
        let form = layui.form;
        let wsServer = 'ws://liy.ws.com/ws';
        let token = localStorage.getItem('user_login_access_token');
        if(!token){
            location.href="/login";
        }
        getInfo(token);
        function getInfo(token){
            $.ajax({
                headers: {
                    Authorization: 'bearer ' + token
                },
                method: "POST",
                url: 'http://liy.ws.com/api/me',
                dataType: 'json',
                data: {},
                success(res) {
                    if(res.code == 1){
                        layer.msg(res.message);
                    }else if(res.code == 1401){
                        layer.msg('登录信息已过期，请重新登录',function(){
                            location.href='/login';
                        });
                    }else{
                        /*let field_data = JSON.stringify(data.field);*/
                        let user = JSON.stringify(res.data);
                        localStorage.setItem('user_info_data',user);
                        onShow();
                    }
                },
                error(e) {
                    console.log(e);
                }
            });
        }

        function onShow(){
            let user = localStorage.getItem('user_info_data');
            let data =  $.parseJSON(user);
            $('.user_name').html(data.user_nickname);
            $('.user_signature').html(data.user_signature);
            $('#user_avatar').attr('src',data.user_avatar);
            console.log(user,data);
        }

    });


    let webSocket = new WebSocket(wsServer);

    webSocket.onopen = function (evt) {
        console.log("Connected to WebSocket server.");
    };

    webSocket.onclose = function (evt) {
        console.log("Disconnected");
    };

    webSocket.onmessage = function (evt) {

        let data = JSON.parse(evt.data) ;
        $('.message-list').append('<pre>'+syntaxHighlight(data)+'</pre>');
        if(data.code == 0){
            if(data.data.type == 'connection'){
                let data = {
                    'action': 'checkToken',
                    'user_id':'13408',
                    'token': token
                };
                webSocket.send(JSON.stringify(data));
            }
        }
    };

    webSocket.onerror = function (evt, e) {
        console.log('Error occured: ' + evt.data);
    };

    $('#check_token_msg').on('click', function () {
        let data = {
            'action': 'checkToken',
            'user_id':'13408',
            'token': 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9saXkuaW0uY29tXC9hcGlcL2xvZ2luIiwiaWF0IjoxNjIzOTE2MTY2LCJleHAiOjE2MjQwMDI1NjYsIm5iZiI6MTYyMzkxNjE2NiwianRpIjoiMTRzaHd6aWpGZVpKN254cSIsInN1YiI6MTM0ODMsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.Hkx4bnkch253Yx6yx1qU1E14Qn4bXlRDWv92VWJdq0Q'
        };
        webSocket.send(JSON.stringify(data));
    });

    $('#send_data_msg').on('click', function () {
        let data = {
            'action':'sendMessage',
            'user_id':'13408',
            'message_type':'text',
            'content':'what are you 弄啥类？'
        };
        webSocket.send(JSON.stringify(data));
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


});
