$(document).ready(function () {

    layui.use(['layer', 'flow'], function () {
        let layer = layui.layer;
        let flow = layui.flow;
        let API_URL = config.API_URL;
        let page = 1;
        let token = localStorage.getItem('user_login_access_token');
        if (!token) {
            location.href = "/login";
        }

        flow.lazyimg();
        flow.load({
            elem: '#flow_list_div' //指定列表容器
            , done: function (page, next) { //到达临界点（默认滚动触发），触发下一页
                let lis = [];
                $.ajax({
                    headers: {
                        Authorization: 'bearer ' + token
                    },
                    method: "POST",
                    url: API_URL + 'friend/list',
                    dataType: 'json',
                    data: {'page': page},
                    success(res) {
                        if (res.code == 1) {
                            layer.msg(res.message, {time: 2000}, function () {
                                window.history.back();
                            });
                        } else if (res.code == 1401) {
                            layer.msg('登录信息已过期，请重新登录', function () {
                                location.href = '/login';
                            });
                        } else {
                            let list = res.data.list;

                            layui.each(list, function (index, item) {
                                let html = '<div class="layui-row friend_item" chat_sn="'+item.chat_sn+'">' +
                                    '    <div class="layui-col-xs2">' +
                                    '        <div class="user_avatar"><img src="'+item.user_avatar+'"/></div>' +
                                    '    </div>' +
                                    '    <div class="layui-col-xs8">' +
                                    '        <div class="item_content">' +
                                    '            <div class="user_nickname">'+item.user_nickname+'</div>' +
                                    '            <div class="last_message">'+item.text+'</div>' +
                                    '        </div>' +
                                    '    </div>' +
                                    '    <div class="layui-col-xs2">' +
                                    '        <div class="message_tip">' +
                                    '            <div class="last_time">' +
                                    '                <span class="layui-badge layui-bg-gray">'+item.last_at+'</span>' +
                                    '            </div>' +
                                    '            <div class="unread_number">';
                                    if(item.unread_number > 0){
                                        html = html+'<span class="layui-badge layui-bg-red">'+item.unread_number+'</span>';
                                    }
                                    html = html+'</div> </div> </div> </div>';
                                lis.push(html);
                            });
                            //执行下一页渲染，第二参数为：满足“加载更多”的条件，即后面仍有分页
                            //pages为Ajax返回的总页数，只有当前页小于总页数的情况下，才会继续出现加载更多
                            next(lis.join(''), page < res.data.pages);

                        }
                    },
                    error(e) {
                        console.log(e);
                    }
                });

            }
        });

        $(document).on('click', '.click_back', function () {
            window.history.back();
        });


        $(document).on('click', '.friend_item', function () {
            let chat_sn = this.getAttribute('chat_sn');
            if (chat_sn) {
                location.href = '/friend/room/' + chat_sn;
            }
        });





    });


});
