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
                    url: API_URL + 'home/party/collection/list',
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
                                let html = '<div class="layui-col-xs6 flow_item" id="flow_item_' + item._id + '" chat_sn="' + item.chat_sn + '">' +
                                    '    <div class="party_leader_avatar" chat_sn="' + item.chat_sn + '">' +
                                    '        <img src="' + item.leader_user_avatar + '">' +
                                    '    </div>' +
                                    '    <div class="party_content">' +
                                    '        <div class="party_title" chat_sn="' + item.chat_sn + '">' + item.party_title + '</div>' +
                                    '        <div class="party_remark" chat_sn="' + item.chat_sn + '">' + item.party_content + '</div>' +
                                    '        <div class="party_bottom">' +
                                    '            <div class="create_at">' + item.created_at + '</div>' +
                                    '            <div class="un_collection" collection_id="' + item._id + '">删除</div>' +
                                    '        </div>\n' +
                                    '    </div>\n' +
                                    '</div>';
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

        $(document).on('click', '.un_collection', function () {
            let collection_id = this.getAttribute('collection_id');
            layer.confirm('确定要删除吗？', {icon: 3, title: ''}, function () {
                unCollection(collection_id);
            });
        });
        $(document).on('click', '.party_leader_avatar', function () {
            let chat_sn = this.getAttribute('chat_sn');
            if (chat_sn) {
                location.href = '/home/room/' + chat_sn;
            }
        });
        $(document).on('click', '.party_remark', function () {
            let chat_sn = this.getAttribute('chat_sn');
            if (chat_sn) {
                location.href = '/home/room/' + chat_sn;
            }
        });
        $(document).on('click', '.party_title', function () {
            let chat_sn = this.getAttribute('chat_sn');
            if (chat_sn) {
                location.href = '/home/room/' + chat_sn;
            }
        });

        function unCollection(collection_id) {
            $.ajax({
                headers: {
                    Authorization: 'bearer ' + token
                },
                method: "POST",
                url: API_URL + 'home/party/collection/del',
                dataType: 'json',
                data: {'collection_id': collection_id},
                success(res) {
                    if (res.code == 1) {
                        layer.msg(res.message);
                    } else if (res.code == 1401) {
                        layer.msg('登录信息已过期，请重新登录', function () {
                            location.href = '/login';
                        });
                    } else {
                        let item = 'flow_item_' + collection_id;
                        $('#' + item).remove();
                        layer.msg('删除成功', {time: 1500});
                    }
                },
                error(e) {
                    console.log(e);
                }
            });
        }


    });


});
