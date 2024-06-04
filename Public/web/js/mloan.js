$(function(){

//提交数据
$("#SendPost").bind('click',function(){
    // layer.msg('0');
    var username = $('#username').val();
    if($("#phone").val()==""){
        layer.tips('请输入手机号码', '#phone', {
            tips: [3, '#ff3146']
        });
        $('#phone').focus();
        return false;
    }
    var phone = $('#phone').val();
    if(phone.length!=11){
        layer.tips('手机号码长度有误', '#phone', {
            tips: [3, '#ff3146']
        });
        return false;
    }
    if(!(/^1[23456789]\d{9}$/.test(phone))){
        layer.tips('手机号码格式错误', '#phone', {
            tips: [3, '#ff3146']
        });
        return false;
    };
    if($('#code').val()==""){
        layer.tips('验证码不能为空', '#code', {
            tips: [3, '#ff3146']
        });
        return false;
    }
    
    var title = $('#title').val();
    var code = $('#code').val();
    var money = $('#money').val();
    var ac = 'submit';
    $.ajax({
        type:'post',
        url:"https://api.chuanronghui.com/ajax.php",
        data:{username:username,phone:phone,ac:ac,code:code,title:title,money:money},
        dataType:"json",
        async: false,
        success:function(data){
            if(data.code == 401){
                layer.msg(data.info);
                return false;
            }
            if(data.code == 200){
                layer.msg(data.info);
                
                setTimeout(function(){
                    window.location.reload();
                },3000);
                return false;
            }
        },error:function(){
            
        }
    })
    
})

//发送验证码
var InterValObj;
var count = 60;
var curCount;
$("#SendCode").bind("click", function(){
    // console.log('loading');
    
    if($("#phone").val()==""){
        layer.tips('请输入手机号码', '#phone', {
            tips: [3, '#ff3146']
        });
        $('#phone').focus();
        return false;
    }
    var phone = $('#phone').val();
    if(phone.length!=11){
        layer.tips('手机号码长度有误', '#phone', {
            tips: [3, '#ff3146']
        });
        return false;
    }
    if(!(/^1[23456789]\d{9}$/.test(phone))){
        layer.tips('手机号码格式错误', '#phone', {
            tips: [3, '#ff3146']
        });
        return false;
    };
    
    
    var ac = 'code';
    var url = window.location.href;
    $.ajax({
        type:'post',
        url:"https://api.chuanronghui.com/ajax.php",
        data:{phone:phone,ac:ac,url:url},
        dataType:"json",
        async: false,
        success:function(data){
            if(data.code==200){
                curCount = count;
                $("#SendCode").attr("disabled", "true");
                $("#SendCode").text(curCount+'s');
                InterValObj = window.setInterval(SetRemainTime, 1000);
                
                layer.msg('发送成功!');
            }
            if(data.code == 401){
                layer.msg(data.info);
            }
        },error:function(){
            
        }
    })
    
});

function SetRemainTime() {
    if (curCount == 0) {
        window.clearInterval(InterValObj);
        $("#SendCode").removeAttr("disabled");
        $("#SendCode").val("重新获取");
    }else{
        curCount--;
        $("#SendCode").val(curCount+'s');
    }
}

})
