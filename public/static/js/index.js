$(document).ready(function () {

    layui.use('slider', function () {
        let slider = layui.slider;

        let token = localStorage.getItem('user_login_access_token');
        if (!token) {
            location.href = "/login";
        }
        initSocket();
        getInfo(token);

        function getInfo(token) {
            $.ajax({
                headers: {
                    Authorization: 'bearer ' + token
                },
                method: "POST",
                url: 'http://liy.ws.com/api/me',
                dataType: 'json',
                data: {},
                success(res) {
                    if (res.code == 1) {
                        layer.msg(res.message);
                    } else if (res.code == 1401) {
                        layer.msg('登录信息已过期，请重新登录', function () {
                            location.href = '/login';
                        });
                    } else {
                        /*let field_data = JSON.stringify(data.field);*/
                        let user = JSON.stringify(res.data);
                        localStorage.setItem('user_info_data', user);
                        onShow();
                    }
                },
                error(e) {
                    console.log(e);
                }
            });
        }

        function onShow() {
            let user = localStorage.getItem('user_info_data');
            let data = $.parseJSON(user);
            $('.user_name').html(data.user_nickname);
            $('.user_signature').html(data.user_signature);
            $('#user_avatar').attr('src', data.user_avatar);
            //console.log(user, data);
        }

        //getDaySign();

        function getDaySign() {
            $.ajax({
                method: "POST",
                url: 'http://liy.ws.com/api/home/day/sign',
                dataType: 'json',
                data: {},
                success(res) {
                    if (res.code == 1) {
                        layer.msg(res.message);
                    } else if (res.code == 1401) {
                        layer.msg('登录信息已过期，请重新登录', function () {
                            location.href = '/login';
                        });
                    } else {
                        /*let field_data = JSON.stringify(data.field);*/
                        let data = res.data;
                        console.log(data[0].user_name);
                    }
                },
                error(e) {
                    console.log(e);
                }
            });
        }

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
                }

            };

            webSocket.onerror = function (evt, e) {
                console.log('Error occured: ' + evt.data);
            };
        }

        function headerCheck(webSocket) {
            let data = {
                'action': 'ping',
            };
            let interVal = setInterval(() => {
                webSocket.send(JSON.stringify(data));
            }, 30000);


        }

        $('.rand-party').on('click',function(){
            location.href = '/home/room/a529437688a5d851f426519301233a05';
        });


    });


});
