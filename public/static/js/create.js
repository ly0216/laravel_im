$(document).ready(function () {

    layui.use(['layer','element','form'], function () {
        let layer = layui.layer;
        let element = layui.element;
        let form = layui.form;
        let API_URL = config.API_URL;
        let token = localStorage.getItem('user_login_access_token');
        if (!token) {
            location.href = "/login";
        }
        element.on('tab(background-image)', function(){
            location.hash = 'background-image='+ this.getAttribute('lay-id');
            let lay_id = this.getAttribute('lay-id');
            if(lay_id == 11){
                $('#background_image').val('https://images.jobslee.top/storage/images/backgroundimage/bg-11.jpeg')
            }else if(lay_id == 10){
                $('#background_image').val('https://images.jobslee.top/storage/images/backgroundimage/bg-10.jpeg')
            }else if(lay_id == 9){
                $('#background_image').val('https://images.jobslee.top/storage/images/backgroundimage/bg-9.jpeg')
            }else if(lay_id == 23){
                $('#background_image').val('https://images.jobslee.top/storage/images/backgroundimage/bg-23.jpeg')
            }else if(lay_id == 22){
                $('#background_image').val('https://images.jobslee.top/storage/images/backgroundimage/bg-22.jpg')
            }else{
                $('#background_image').val('https://images.jobslee.top/storage/images/backgroundimage/bg-11.jpeg')
            }
            console.log(this.getAttribute('lay-id'))
        });
        form.on('submit(createParty)', function(data){
            /*layer.msg(JSON.stringify(data.field));*/
            $.ajax({
                headers: {
                    Authorization: 'bearer ' + token
                },
                method: "POST",
                url: API_URL + 'home/create/party',
                dataType: 'json',
                data: data.field,
                success(res) {
                    if (res.code == 1) {
                        layer.msg(res.message);
                    } else if (res.code == 1401) {
                        layer.msg('登录信息已过期，请重新登录', function () {
                            location.href = '/login';
                        });
                    } else {
                        layer.msg('派对创建成功，请等待管理员审核',{time:1500},function(){
                           location.href = '/home/index';
                        });

                    }
                },
                error(e) {
                    console.log(e);
                }
            });

            return false;
        });
        $('.click_back').on('click',function(){
           location.href = '/home/index';
        });

    });


});
