$(document).ready(function () {

    layui.use('slider', function () {
        let slider = layui.slider;

        let token = localStorage.getItem('user_login_access_token');
        if (!token) {
            location.href = "/login";
        }

        initSocket();
        scrollToBottom();
        join();

        //初始化连接socket
        function initSocket() {
            console.log('连接socket');
            let wsServer = 'ws://liy.ws.com/ws?token=' + token;
            let webSocket = new WebSocket(wsServer);
            webSocket.onopen = function (evt) {
                console.log("Connected to WebSocket server.");
                if (evt.isTrusted && evt.returnValue) {
                    console.log('socket 连接成功');
                    headerCheck(webSocket);
                } else {
                    console.log('socket 连接失败');
                }
            };
            webSocket.onclose = function (evt) {
                console.log("Disconnected");
            };

            webSocket.onmessage = function (evt) {

                let data = JSON.parse(evt.data);
                if (data.action == 'login') {
                    location.href = '/login';
                } else if (data.action == 'ping') {
                    //headerCheck(webSocket);
                } else if (data.action == 'error') {
                    console.log(data);
                } else if (data.action == "chatMessage") {

                }

            };

            webSocket.onerror = function (evt, e) {
                console.log('Error occured: ' + evt.data);
            };
        }

        //心跳
        function headerCheck(webSocket) {
            let data = {
                'action': 'ping',
            };
            let interVal = setInterval(() => {
                webSocket.send(JSON.stringify(data));
            }, 30000);


        }

        function join() {
            let chat_sn = $('#chat_sn').val();
            $.ajax({
                headers: {
                    Authorization: 'bearer ' + token
                },
                method: "POST",
                url: 'http://liy.ws.com/api/home/room/join',
                dataType: 'json',
                data: {'chat_sn': chat_sn},
                success(res) {
                    if (res.code == 1) {
                        layer.msg(res.message);
                    } else if (res.code == 1401) {
                        layer.msg('登录信息已过期，请重新登录', function () {
                            location.href = '/login';
                        });
                    } else {

                    }
                },
                error(e) {
                    console.log(e);
                }
            });
        }

        //会话消息滚动到底部
        function scrollToBottom() {
            //var div = document.getElementById('chatCon');
            let div = document.getElementById('content_div');
            div.innerHTML = div.innerHTML + '<br />';
            div.scrollTop = div.scrollHeight;
        }

        //发送消息
        $('#send_message').on('click', function () {
            let text = $('#content_message').val();
            if (!text) {
                layer.msg('消息不能为空');
                return false;
            }
            let content = JSON.stringify({'text': text});
            let chat_sn = $('#chat_sn').val();
            if(!chat_sn){
                layer.msg('缺少参数');
                return false;
            }
            $.ajax({
                headers: {
                    Authorization: 'bearer ' + token
                },
                method: "POST",
                url: 'http://liy.ws.com/api/home/send/message',
                dataType: 'json',
                data: {'chat_sn': chat_sn,'content':content},
                success(res) {
                    if (res.code == 1) {
                        layer.msg(res.message);
                    } else if (res.code == 1401) {
                        layer.msg('登录信息已过期，请重新登录', function () {
                            location.href = '/login';
                        });
                    } else {
                        layer.msg('消息发送成功');
                    }
                    $('#content_message').val('');
                    return false;
                },
                error(e) {
                    console.log(e);
                }
            });

        });

    });


});
