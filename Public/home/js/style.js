const apiUrl = "https://api.chuanronghui.com/ajax.php";
const Url = "https://tb.53kf.com/code/client/92e9380d0b0cea8a4202ace3ced4c3939/3";
const interval = 60;
let curCount;
$(".tj").click(function () {
    const data = bdData();

    if (data.status === 0) {
        layer.msg(data.info);
        return;
    }

    data.ac = 'submit';
    data.code = $("#captcha").val();

    if (data.code === "") {
        layer.msg("请输入验证码");
        $('#captcha').focus();
        return;
    }

    sendAjaxRequest(data);
});

$("#get_code").click(function () {
    const data = bdData();

    if (data.status === 0) {
        layer.msg(data.info);
        return;
    }

    data.ac = 'code';
    sendAjaxRequest(data);
});

function bdData() {
    const data = {};
    data.username = $('#name').val();
    data.phone = $("#phone").val();
    // data.money    = $("#amount").val();
    data.title = "立即申请";
    data.url = window.location.href;
    data.status = 1;

    if (data.username === "") {
        $('#name').focus();
        data.status = 0;
        data.info = "请输入姓名";
        return data;
    }

    if (!(/^1[3456789]\d{9}$/.test(data.phone))) {
        layer.msg("手机号输入不正确");
        $("#phone").focus();
        data.status = 0;
        data.info = "手机号输入不正确";
        return data;
    }

    // if (data.money === "") {
    //     layer.msg("请输入额度");
    //     $('#amount').focus();
    //     data.status = 0;
    //     data.info = "请输入额度";
    //     return data;
    // }

    return data;
}

$("#SendCode").click(function () {
    const data = {};
    data.username = "游客";
    data.phone = $("#phone_dk").val();
    data.url = window.location.href;
    if (!(/^1[3456789]\d{9}$/.test(data.phone))) {
        layer.msg("手机号输入不正确");
        $("#phone_dk").focus();
        return;
    }

    data.ac = 'code';
    sendAjaxRequest(data);
});

$("#SendPost").click(function () {
    const data = {};
    data.username = "游客";
    data.title = "立即申请";
    data.phone = $("#phone_dk").val();
    data.url = window.location.href;

    if (!(/^1[3456789]\d{9}$/.test(data.phone))) {
        layer.msg("手机号输入不正确");
        $("#phone_qs").focus();
        return;
    }

    data.code = $("#code").val();

    if (data.code === "") {
        layer.msg("请输入验证码");
        $('#code').focus();
        return;
    }

    data.ac = 'submit';
    sendAjaxRequest(data);
});

$("#SendCode_web").click(function () {
    const data = {};
    data.phone = $("#phone").val();
    data.ac = 'code';
    data.url = window.location.href;
    if (!(/^1[3456789]\d{9}$/.test(data.phone))) {
        layer.msg("手机号输入不正确");
        $("#phone").focus();
        return;
    }

    sendAjaxRequest(data);
});

$("#SendPost_web").click(function () {
    const data = {};
    data.code = $("#code").val();
    data.username = $('#name').val() || "游客";
    data.url = window.location.href;
    if (data.code === "") {
        layer.msg("请输入验证码");
        $('#code').focus();
        return;
    }

    data.phone = $("#phone").val();
    data.ac = 'submit';
    data.title = '立即申请';
    sendAjaxRequest(data);
});

$("#SendCode_qs").click(function () {
    const data = {};
    data.username = "游客";
    data.phone = $("#phone_qs").val();
    data.url = window.location.href;
    if (!(/^1[3456789]\d{9}$/.test(data.phone))) {
        layer.msg("手机号输入不正确");
        $("#phone_qs").focus();
        return;
    }

    data.ac = 'code';
    sendAjaxRequest(data);
});


$("#SendPost_qs").click(function () {
    const data = {};
    data.username = "游客";
    data.title = "立即申请";
    data.phone = $("#phone_qs").val();
    data.url = window.location.href;

    if (!(/^1[3456789]\d{9}$/.test(data.phone))) {
        layer.msg("手机号输入不正确");
        $("#phone_qs").focus();
        return;
    }

    data.code = $("#code").val();

    if (data.code === "") {
        layer.msg("请输入验证码");
        $('#code').focus();
        return;
    }

    data.ac = 'submit';
    sendAjaxRequest(data);
});


$("#get_code_news").click(function () {
    const data = {};
    data.phone = $("#phone_news").val();
    data.url = window.location.href;
    if (!(/^1[3456789]\d{9}$/.test(data.phone))) {
        layer.msg("手机号输入不正确");
        $("#phone_qs").focus();
        return;
    }

    data.ac = 'code';
    sendAjaxRequest(data);
});

$("#tj_news").click(function () {
    const data = {};
    data.username = $("#name_news").val();
    data.title = "立即申请";
    data.phone = $("#phone_news").val();
    data.text = $("#news_textarea").val();
    data.url = window.location.href;
    data.username = data.username + data.text;

    if (!(/^1[3456789]\d{9}$/.test(data.phone))) {
        layer.msg("手机号输入不正确");
        $("#phone_qs").focus();
        return;
    }

    data.code = $("#code_news").val();

    if (data.code === "") {
        layer.msg("请输入验证码");
        $('#code').focus();
        return;
    }

    if (data.username === "") {
        layer.msg("请输入姓名");
        $('#name_news').focus();
        return;
    }

    data.ac = 'submit';
    sendAjaxRequest(data);
});

function sendAjaxRequest(data) {


    if (data.ac == "code") {
        data.username = '游客';
    }


    $.ajax({
        type: 'post',
        url: apiUrl,
        data: data,
        dataType: "json",
        async: false,
        success: function (result) {
            console.log(result);

            if (result.code === 200) {

                if (data.ac == "code") {
                    code();
                }

                layer.msg(result.info);

                if (data.ac == "submit") {
                    resetForm();
                }
            } else {
                layer.msg(result.info);
            }
        },
        error: function (e) {
            console.log(e);
        }
    });
}

$(".t53").click(function () {

    window.open(Url);

});

function resetForm() {
    $('#name').val('');
    $("#phone").val('');
    // $("#amount").val('');
    $("#captcha").val('');
    $("#phone_dk").val('');
    $("#code").val('');
    $("#phone_qs").val('');
    $("#name_news").val('');
    $("#phone_news").val('');
    $("#news_textarea").val('');
    $("#code_news").val('');
}

function code() {
    curCount = interval;
    $(".yzm").attr("disabled", true);
    $(".yzm").val(`${curCount}秒后重发`);
    InterValObj = window.setInterval(open_SetRemainTime, 1000);
}

function open_SetRemainTime() {
    if (curCount === 0) {
        window.clearInterval(InterValObj);
        $(".yzm").removeAttr("disabled");
        $(".yzm").html("重新获取");
        $(".yzm").val("重新获取");
    } else {
        curCount--;
        console.log(curCount);
        $(".yzm").html(curCount);
        $(".yzm").val(`${curCount}秒后重发`);
    }
}

$('.xieyi').on('click', function () {
    $('.modal').addClass('show');
});
$('#close').on('click', function () {
    $('.modal').removeClass('show');
})
$('.pushInput').on('click', function () {
    $('.modal').removeClass('show');
})

function close_video() {
    var v = document.getElementById('video');	//获取视频节点
    $('.videos').hide();						//点击关闭按钮关闭暂停视频
    v.pause();
    $('.videos').html();
}

/* 1.1视频 */
$('.videolist').each(function () { //遍历视频列表
    $(this).hover(function () { //鼠标移上来后显示播放按钮
        $(this).find('.videoed').show();
    }, function () {
        $(this).find('.videoed').hide();
    });
    $(this).click(function () { //这个视频被点击后执行
        var img = $(this).attr('vpath');//获取视频预览图
        console.log(img)
        var video = $(this).attr('ipath');//获取视频路径
        $('.videos').html("<video id=\"video\" poster='" + img + "' style='width: 640px' src='" + video + "' preload=\"auto\" controls=\"controls\" autoplay=\"autoplay\"></video><img onClick=\"close_video()\" class=\"vclose\" src=\"/Public/home/img/close.png\" width=\"25\" height=\"25\"/>");
        $('.videos').show();
    });
});

// Banner 勾选协议
$(".like .checked-ui .label").on('click',function() {
    var txt = $(this).parent().find('.ui-tit').text();
    console.log(txt)
    if ($(this).parent().find('.label').hasClass('active')) {
        $(this).parent().find('.label').removeClass('active');

    } else {
        $(this).parent().find('.label').addClass('active');

    }
})

$(".open_bd").on('click',function() {
    $("#if").attr("style","display:block" );
    open_bd();
})
function open_bd(){

    layer.open({
        type:1,
        offset:'auto',
        title:"",
        shade: 0.3, 					//遮罩透明度
        maxmin: false, 				//允许全屏最小化
        anim: 5, 					//0-6的动画形式，-1不开启
        // area: ['50%','50%'], 	//宽高
        resize:false,				//允许被拉伸
        skin: 'window', 			//注意这里，靠这个css自定义样式！！！！！
        scrollbar: false, //禁止滚动条 或者 弹窗页面》=页面大小，就没有滚动条))
        content:$("#if"),
        cancel: function (index, layero) {
            $("#if").attr("style","display:none !important" );
            console.log(111);
        },
        end: function (){
            $("#if").attr("style","display:none !important" );
        }
    })

}
