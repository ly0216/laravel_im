$(document).ready(function(){
    layui.use('form', function(){
        var form = layui.form;
        //监听提交
        form.on('submit(signIn)', function(data){
            let field_data = JSON.stringify(data.field);
            layer.msg(field_data);
            return false;
        });
    });
});
