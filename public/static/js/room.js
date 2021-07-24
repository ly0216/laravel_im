
    layui.use('layer', function () {
        let layer = layui.layer;

        let token = localStorage.getItem('user_login_access_token');
        if (!token) {
            layer.msg('登录信息已过期，请重新登录',{time:1500},function(){
                location.href = "/login";
            });

        }
        let _UID = 0;
        let userInfo = localStorage.getItem('user_info_data');
        if(!userInfo){
            layer.msg('用户信息已过期，请重新登录',{time:1500},function(){
                location.href = "/login";
            });
        }
        let user_info = JSON.parse(userInfo);
        _UID = user_info.id;
        document.addEventListener('visibilitychange', function() {
            // 页面变为不可见时触发
            if (document.visibilityState == 'hidden') {
                console.log('离开页面');
            }
            // 页面变为可见时触发
            if (document.visibilityState == 'visible') {
                console.log('回到页面');
                scrollToBottom();
            }
            let isHidden = document.hidden;
            if(isHidden){
                console.log('离开页面001');
            } else {
                console.log('回到页面001');
                scrollToBottom();
            }
        });

        initSocket();
        scrollToBottom();
        join();
        historyMessage();

        //初始化连接socket
        function initSocket() {
            console.log('连接socket');
            //let wsServer = 'ws://liy.ws.com/ws?token=' + token;
            let wsServer = 'wss://im.jobslee.top/wss?token=' + token;
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
                    appendMessage(data);
                    scrollToBottom();

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

        //进入派对
        function join() {
            let chat_sn = $('#chat_sn').val();
            if(!chat_sn){
                layer.msg('无效的派对',{time:1500},function () {
                    window.history.back();
                });
            }
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

        //获取派对历史消息
        function historyMessage() {
            let chat_sn = $('#chat_sn').val();
            if(!chat_sn){
                layer.msg('无效的派对',{time:1500},function () {
                    window.history.back();
                });
            }
            $.ajax({
                headers: {
                    Authorization: 'bearer ' + token
                },
                method: "POST",
                url: 'http://liy.ws.com/api/home/room/history/message',
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
                        let list = res.data;
                        $.each(list,function(idx,item){
                            appendMessage(item);
                            scrollToBottom();//消息滚动到底部
                        });
                        console.log(list);
                    }
                },
                error(e) {
                    console.log(e);
                }
            });
        }

        function appendMessage(message){
            if(message.message_type == 0){//普通消息
                if(message.user_id == _UID){//自己的消息右侧
                    appendSelfMessage(message);
                }else{//其他人的消息右侧
                    appendOtherUserMessage(message);
                }

            }else if(message.message_type == 1){//系统消息
                appendSysMessage(message);
            }
        }
        //追加自己的消息
        function appendSelfMessage(message){
            let html = '<div class="layui-row message_content">' +
                '    <div class="layui-col-xs1">&nbsp;</div>' +
                '    <div class="layui-col-xs9">' +
                '        <div class="layui-row right-name">' +
                '            <div class="chat_user_name">'+message.user_name+'</div>' +
                '        </div>' +
                '        <div class="layui-row">' +
                '            <div class="chat_message right-message">'+message.content.text+'</div>' +
                '        </div>' +
                '    </div>' +
                '    <div class="layui-col-xs2">' +
                '        <div class="chat_user_avatar">' +
                '            <img src="'+message.user_avatar+'">' +
                '        </div>' +
                '    </div>' +
                '</div>';
            $('.message').append(html);
        }
        //追加其他人的消息
        function appendOtherUserMessage(message){
            let html = '<div class="layui-row message_content">' +
                '    <div class="layui-col-xs2">' +
                '        <div class="chat_user_avatar">' +
                '            <img src="'+message.user_avatar+'">' +
                '        </div>' +
                '    </div>' +
                '    <div class="layui-col-xs9">' +
                '        <div class="layui-row">' +
                '            <div class="chat_user_name">'+message.user_name+'</div>\n' +
                '        </div>' +
                '        <div class="layui-row">' +
                '            <div class="chat_message">'+message.content.text+'</div>' +
                '        </div>' +
                '    </div>' +
                '</div>';
            $('.message').append(html);
        }
        //追加系统消息
        function appendSysMessage(message){
            let html = '<div class="layui-row message_content system_message">' +
                '    <div class="layui-col-xs1">&nbsp;</div>' +
                '    <div class="layui-col-xs10">' +
                '        <div>⚜️'+message.content.text+'⚜️</div>' +
                '    </div>\n' +
                '    <div class="layui-col-xs1">&nbsp;</div>' +
                '</div>';
            $('.message').append(html);
        }

        //会话消息滚动到底部
        function scrollToBottom() {
            //var div = document.getElementById('chatCon');
            let div = document.getElementById('content_div');
            //div.innerHTML = div.innerHTML + '<br />';
            console.log(div.scrollHeight);
            div.scrollTop = div.scrollHeight;
        }

        //回车发送消息
        $(document).keydown(function (e) {
            if(e.keyCode == 13){
                sendMessage();
            }
        });

        //点击发送消息
        $('#send_message').on('click', function () {
            sendMessage();
        });

        //发送消息方法
        function sendMessage(){
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
                    scrollToBottom();//消息滚动到底部
                    return false;
                },
                error(e) {
                    console.log(e);
                }
            });
        }

    });


