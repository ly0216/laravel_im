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
                    url: API_URL + 'home/friend/apply/list',
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
                            console.log(list);
                            layui.each(list, function (index, item) {
                                let html = '<div class="layui-row flow_item" id="flow_item_'+item._id+'">' +
                                    '    <div class="layui-col-xs3">' +
                                    '        <div class="user_avatar"><img src="'+item.friend_avatar+'"></div>' +
                                    '    </div>' +
                                    '    <div class="layui-col-xs7">' +
                                    '        <div class="layui-row user_nickname">'+item.friend_nickname+'</div>' +
                                    '        <div class="layui-row data_time">'+item.created_at+'</div>' +
                                    '    </div>' +
                                    '    <div class="layui-col-xs2">' +
                                    '        <div class="layui-row agree_btn" apply_id="'+item._id+'">同意</div>' +
                                    '        <div class="layui-row refuse_btn" apply_id="'+item._id+'">拒绝</div>' +
                                    '    </div>' +
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

        $(document).on('click', '.agree_btn', function () {
            let apply_id = this.getAttribute('apply_id');
            apply(apply_id,1);
        });

        $(document).on('click', '.refuse_btn', function () {
            let apply_id = this.getAttribute('apply_id');
            apply(apply_id,2);
        });

        function apply(apply_id,type) {
            $.ajax({
                headers: {
                    Authorization: 'bearer ' + token
                },
                method: "POST",
                url: API_URL + 'home/friend/apply/do',
                dataType: 'json',
                data: {'apply_id': apply_id,'type':type},
                success(res) {
                    if (res.code == 1) {
                        layer.msg(res.message);
                    } else if (res.code == 1401) {
                        layer.msg('登录信息已过期，请重新登录', function () {
                            location.href = '/login';
                        });
                    } else {
                        let item = 'flow_item_' + apply_id;
                        $('#' + item).remove();
                        layer.msg('操作成功', {time: 1500});
                    }
                },
                error(e) {
                    console.log(e);
                }
            });
        }


    });


});
