$(function () {	
  $("#j_name").val("请输入您的姓名").css('color', '#3c3c3c');  
  textFill($('#j_name'));  
  $('#j_tel').val("请输入手机号").css('color', '#3c3c3c');
  textFill($('#j_tel'));  
  $('#j_yzm').val("请输入验证码").css('color', '#3c3c3c');
  textFill($('#j_yzm')); 
  function textFill(input) {
    var originalvalue = input.val();
    input.focus(function () {
      if ($.trim(input.val()) == originalvalue) {
        input.val('');
      }
    });
    input.blur(function () {
      if ($.trim(input.val()) == '') {
        input.val(originalvalue);
      }
    });
  }


  // 验证
  var cMsg = '';

  function vaild() {
    var result = true;
    var tel = $('#j_tel').val().trim(),
      yzm = $('#j_yzm').val().trim(),
      name = $('#j_name').val().trim(),
	 
      name_reg = /[\u4e00-\u9fa5]/gm,
      money_reg = /^\d+$/,
      tel_reg = /^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/;

    if (tel == '' || tel == undefined || tel == null) {
      $('#j_tel').focus();
      $('.po_text').fadeIn(800).text('请输入手机号').delay(800).fadeOut(800);
      result = false;
    } else if (!tel_reg.test(tel)) {
      $('#j_tel').focus();
      $('.po_text').fadeIn(800).text('请输入正确的手机号').delay(800).fadeOut(800);
      result = false;
    } else if ($.trim(yzm).length != 4) {
      $('#j_yzm').focus();
      $('.po_text').fadeIn(800).text('输入正确的验证码').delay(800).fadeOut(800);
      result = false;
    } else if (!name_reg.test(name)) {
      $('#j_name').focus();
      $('.po_text').fadeIn(800).text('输入正确的姓名').delay(800).fadeOut(800);
      result = false;
    }
    return result;
  }

  function codeVaild() {
    var result = true;
    var tel = $('#j_tel').val().trim(),
      yzm = $('#j_yzm').val().trim(),
      name = $('#j_name').val().trim(),
      name_reg = /[\u4e00-\u9fa5]/gm,
      money_reg = /^\d+$/,
      tel_reg = /^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/;

    if (tel == '' || tel == undefined || tel == null) {
      $('#j_tel').focus();
      $('.po_text').fadeIn(800).text('请输入手机号').delay(800).fadeOut(800);
      result = false;
    } else if (!tel_reg.test(tel)) {
      $('#j_tel').focus();
      $('.po_text').fadeIn(800).text('请输入正确的手机号').delay(800).fadeOut(800);
      result = false;
    } else if (!name_reg.test(name)) {
      $('#j_name').focus();
      $('.po_text').fadeIn(800).text('输入正确的姓名').delay(800).fadeOut(800);
      result = false;
    }
    return result;
  }

  $('.getCd').on('click', function () {
    var res = codeVaild();

    if (res) {
      $('.getCd').attr('disabled', true).addClass('geta');
      $.ajax({
        url: '/api/weboauth/send_mobile_verify/mobile/' + $('#j_tel').val(),
        beforeSend: function () {
          $('.po_text').fadeIn(800).text('发送成功').delay(800).fadeOut(800);
          var num = 120;
          var timer = setInterval(function () {
            num--;
            $('.getCd').val(num + '秒重发');
            if (num <= 0) {
              $('.getCd').attr('disabled', false).removeClass('geta').val('获取验证码');
              clearInterval(timer);
            }
          }, 1000);
        },
        success: function (data) {
          var data = JSON.parse(data);
          if (data.code == 2) {
            cMsg = data.iphone_code;
          } else {
            alert(data.msg);
          }
        }
      })
    }
    return false;
    
  })


  $('.join_sub_btn').on('click', function () {
    var res = vaild(),
      yzm = $('#j_yzm').val().trim();

    if (res) {
      if (yzm == cMsg) {
        $.ajax({
          url: '/api/weboauth/Franchise',
          data: {
            mobile: $('#j_tel').val(),
            name: $('#j_name').val().trim(),
		
            code: yzm,
            type: 2
          },
          success: function (data) {
            var data = JSON.parse(data);
            if (data.status <= 0) {
              $('.po_text').fadeIn(800).text(data.msg).delay(800).fadeOut(800);
            } else {
              $('.po_text').fadeIn(800).text(data.msg).delay(800).fadeOut(800);
              $('input').val('');
            }

          }
        })
      } else {
        $('.po_text').fadeIn(800).text('验证码不正确').delay(800).fadeOut(800);
      }
    }
  })
  return false;
  
})