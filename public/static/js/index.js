$(document).ready(function () {

    layui.use(['layer','flow'], function () {
        let layer = layui.layer;
        let flow = layui.flow;
        let API_URL = config.API_URL;
        let page = 1;
        let token = localStorage.getItem('user_login_access_token');
        if (!token) {
            location.href = "/login";
        }

        getInfo(token);

        function getInfo(token) {
            $.ajax({
                headers: {
                    Authorization: 'bearer ' + token
                },
                method: "POST",
                url: API_URL+'me',
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
        flow.lazyimg();
        flow.load({
            elem: '#flow_list_div' //指定列表容器
            ,done: function(page, next){ //到达临界点（默认滚动触发），触发下一页
                let lis = [];
                $.ajax({
                    headers: {
                        Authorization: 'bearer ' + token
                    },
                    method: "POST",
                    url: API_URL + 'home/room/list',
                    dataType: 'json',
                    data: {'page': page,'is_hot':1},
                    success(res) {
                        if (res.code == 1) {
                            layer.msg(res.message,{time:2000},function(){
                                window.history.back();
                            });
                        } else if (res.code == 1401) {
                            layer.msg('登录信息已过期，请重新登录', function () {
                                location.href = '/login';
                            });
                        } else {
                            let list = res.data.list;
                            layui.each(list, function(index, item){
                                let html = '<div class="layui-col-xs6 flow_item" chat_sn="'+item.chat_sn+'" >' +
                                    '    <div class="label_content">' +
                                    '        <div class="content_body">' +
                                    '            <div class="content_body_title">'+item.title+'</div>' +
                                    '            <div class="content_body_text">' +item.content+ '</div>' +
                                    '        </div>' +
                                    '        <div class="content_text">'+item.user_name+'</div>' +
                                    '    </div>' +
                                    '    <div class="label_user_avatar">' +
                                    '        <img src="'+item.user_avatar+'" lay-src="'+item.user_avatar+'">' +
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

        //创建派对
        $('#create_party').on('click',function(){
            location.href = '/home/party/create';
        });
        //派对列表
        $('#party_list').on('click',function(){
            layer.msg('正在努力建设中。。。。',{time:1500});
        });
        //随机加入派对
        $('#rand_party').on('click',function(){
            $.ajax({
                headers: {
                    Authorization: 'bearer ' + token
                },
                method: "POST",
                url: API_URL+'home/random/join',
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
                        location.href = '/home/room/'+res.data;

                    }
                },
                error(e) {
                    console.log(e);
                }
            });
        });


        //点击进入派对
        $(document).on('click','.flow_item',function(){
           let chat_sn = this.getAttribute('chat_sn');
           if(chat_sn){
               location.href = '/home/room/'+chat_sn;
           }

        });


    });


});
