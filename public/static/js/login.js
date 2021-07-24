$(document).ready(function(){
    layui.use('form', function(){
        let form = layui.form;
        let API_URL = config.API_URL;
        //监听提交
        form.on('submit(signIn)', function(data){
            /*let field_data = JSON.stringify(data.field);*/
            let token = '';
            $.ajax({
                headers: {
                    Authorization: 'bearer ' + token
                },
                method: "POST",
                /*url: 'https://im.jobslee.top/api/login',*/
                url:API_URL+'login',
                dataType: 'json',
                data: data.field,
                success(res) {
                    if(res.code == 1){
                        layer.msg(res.message);
                    }else{
                        let data = res.data;
                        if(data.access_token){
                            localStorage.setItem('user_login_access_token',data.access_token);
                            layer.msg('登录成功',{time:1500},function(){
                                location.href = '/home/index';
                            });
                        }else{
                            layer.msg('登录失败');
                        }
                    }
                    console.log(res);
                },
                error(e) {
                    console.log(e);
                }
            });
            return false;
        });
    });
});
