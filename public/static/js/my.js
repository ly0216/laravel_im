$(document).ready(function () {

    layui.use(['layer', 'flow'], function () {
        let layer = layui.layer;
        let flow = layui.flow;
        let API_URL = config.API_URL;
        let page = 1;
        let is_hidden = 1;
        let token = localStorage.getItem('user_login_access_token');
        if (!token) {
            location.href = "/login";
        }
        onShow();

        function onShow() {
            let user = localStorage.getItem('user_info_data');
            if (user) {
                let userInfo = JSON.parse(user);
                $('#user_nickname').val(userInfo.user_nickname);
                $('#user_sign').val(userInfo.user_signature);
                $('#user_avatar').attr('src', userInfo.user_avatar);

            }

        }

        //获取头像列表
        $(document).on('click', '.show_avatar_list', function () {
            $('#avatar_list_div').show();
        });

        flow.lazyimg();
        flow.load({
            elem: '#avatar_list_div' //指定列表容器
            , done: function (page, next) { //到达临界点（默认滚动触发），触发下一页
                let lis = [];
                $.ajax({
                    headers: {
                        Authorization: 'bearer ' + token
                    },
                    method: "POST",
                    url: API_URL + 'home/avatar/list',
                    dataType: 'json',
                    data: {'page': page, 'is_hot': 1},
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
                            $('.number_n').text(res.data.man);
                            $('.number_m').text(res.data.girl);
                            let list = res.data.list;
                            layui.each(list, function (index, item) {
                                let html = '<div class="layui-col-xs4 avatar_item" avatar_url="' + item.image_url + '" avatar_id="' + item._id + '">' +
                                    '    <div class="layui-col-xs4 avatar_item_val">' +
                                    '        <img src="' + item.image_url + '">' +
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

        $(document).on('click', '.avatar_item', function () {
            let avatar_url = this.getAttribute('avatar_url');
            let avatar_id = this.getAttribute('avatar_id');
            console.log(avatar_url, avatar_id);
            $('#user_avatar').attr('src', avatar_url);
            $('#user_info_avatar').val(avatar_url);
            $('#avatar_list_div').hide();
        });

        //修改基本信息
        $(document).on('click', '.sub_user_base', function () {
            let user_nickname = $('#user_nickname').val();
            let user_sign = $('#user_sign').val();
            if (!user_nickname && !user_sign) {
                layer.msg('请填写要修改的内容');
                return false;
            }
            let user_avatar = $('#user_info_avatar').val();

            $.ajax({
                headers: {
                    Authorization: 'bearer ' + token
                },
                method: "POST",
                url: API_URL + 'home/change/user/info',
                dataType: 'json',
                data: {'user_signature': user_sign, 'user_nickname': user_nickname,'user_avatar':user_avatar},
                success(res) {
                    if (res.code == 1) {
                        layer.msg(res.message);
                    } else if (res.code == 1401) {
                        layer.msg('登录信息已过期，请重新登录', function () {
                            location.href = '/login';
                        });
                    } else {
                        let user = localStorage.getItem('user_info_data');
                        let userInfo = JSON.parse(user);
                        userInfo.user_nickname = user_nickname;
                        userInfo.user_signature = user_sign;
                        let userJson = JSON.stringify(userInfo);
                        localStorage.setItem('user_info_data', userJson);


                        layer.msg('修改成功');
                    }
                    return false;
                },
                error(e) {
                    console.log(e);
                }
            });


        });

        //修改密码
        $(document).on('click', '.sub_change_password', function () {
            let old_password = $('#old_password').val();
            let new_password = $('#new_password').val();
            let rep_password = $('#rep_password').val();
            if (!old_password || !new_password || !rep_password) {
                layer.msg('请将密码信息填写完整');
                return false;
            }
            if (old_password == new_password) {
                layer.msg('新密码不可与旧密码相同');
                return false;
            }
            if (new_password != rep_password) {
                layer.msg('两次密码输入不一致');
                return false;
            }

            $.ajax({
                headers: {
                    Authorization: 'bearer ' + token
                },
                method: "POST",
                url: API_URL + 'home/change/user/pass',
                dataType: 'json',
                data: {'old_password': old_password, 'new_password': new_password, 'rep_password': rep_password},
                success(res) {
                    if (res.code == 1) {
                        layer.msg(res.message);
                    } else if (res.code == 1401) {
                        layer.msg('登录信息已过期，请重新登录', function () {
                            location.href = '/login';
                        });
                    } else {
                        layer.msg('密码修改成功，请重新登录', {time: 1500}, function () {
                            localStorage.removeItem('user_login_access_token');
                            localStorage.removeItem('user_info_data');
                            location.href = '/login';
                        });
                    }
                    return false;
                },
                error(e) {
                    console.log(e);
                }
            });
        });

        $(document).on('click', '.change_password_btn', function () {
            if (is_hidden == 1) {
                is_hidden = 2;
                $('.change_password').show();
            } else {
                is_hidden = 1;
                $('.change_password').hide();
            }

        });
        $(document).on('click', '.click_back', function () {
            window.history.back();
        });

        $(document).on('click', '.agree_btn', function () {
            let apply_id = this.getAttribute('apply_id');
            apply(apply_id, 1);
        });

        $(document).on('click', '.refuse_btn', function () {
            let apply_id = this.getAttribute('apply_id');
            apply(apply_id, 2);
        });

        function apply(apply_id, type) {
            $.ajax({
                headers: {
                    Authorization: 'bearer ' + token
                },
                method: "POST",
                url: API_URL + 'home/friend/apply/do',
                dataType: 'json',
                data: {'apply_id': apply_id, 'type': type},
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
