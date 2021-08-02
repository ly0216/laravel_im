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
