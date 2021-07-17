$(document).ready(function(){

    let wsServer = 'ws://liy.ws.com/ws';
    let webSocket = new WebSocket(wsServer);
    let token = localStorage.getItem('user_login_access_token');
    if(!token){

    }
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
