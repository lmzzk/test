$(function () {

  $('.type_box li').hover(function () {
    $(this).addClass('active').siblings().removeClass('active')
    $(this).parents('.type_box').siblings('.type_box_item').eq($(this).index()).removeClass('dn').siblings('.type_box_item').addClass('dn')
  });
 

  // 头部新增城市
  $('.city_show').on('click', function () {
    $('.city_t>.city_hot').toggleClass('dn');
  })



  // 首页带融资讯
  $('.info_cont_list li:even>a').append('<span></span>');
  $('.info_cont_list li:odd>a').addClass('act');

  // 首页数字滚动
  $.fn.countTo = function (a) {
    a = a || {};
    return $(this).each(function () {
      var c = $.extend({},
        $.fn.countTo.defaults, {
          from: $(this).data("from"),
          to: $(this).data("to"),
          speed: $(this).data("speed"),
          refreshInterval: $(this).data("refresh-interval"),
          decimals: $(this).data("decimals")
        },
        a);
      var h = Math.ceil(c.speed / c.refreshInterval),
        i = (c.to - c.from) / h;
      var j = this,
        f = $(this),
        e = 0,
        g = c.from,
        d = f.data("countTo") || {};
      f.data("countTo", d);
      if (d.interval) {
        clearInterval(d.interval)
      }
      d.interval = setInterval(k, c.refreshInterval);
      b(g);

      function k() {
        g += i;
        e++;
        b(g);
        if (typeof (c.onUpdate) == "function") {
          c.onUpdate.call(j, g)
        }
        if (e >= h) {
          f.removeData("countTo");
          clearInterval(d.interval);
          g = c.to;
          if (typeof (c.onComplete) == "function") {
            c.onComplete.call(j, g)
          }
        }
      }

      function b(m) {
        var l = c.formatter.call(j, m, c);
        f.html(l)
      }
    })
  };
  $.fn.countTo.defaults = {
    from: 0,
    to: 0,
    speed: 1000,
    refreshInterval: 100,
    decimals: 0,
    formatter: formatter,
    onUpdate: null,
    onComplete: null
  };

  function formatter(b, a) {
    return b.toFixed(0)
  }
  $(".count-number").data("countToOptions", {
    formatter: function (b, a) {
      return b.toFixed(0).replace(/\B(?=(?:\d{3})+(?!\d))/g, ",")
    }
  });
  $(".timer").each(count);

  function count(a) {
    var b = $(this);
    a = $.extend({},
      a || {},
      b.data("countToOptions") || {});
    b.countTo(a)
  };



  // 首页
  $('.propic1 a').on('mouseenter', function () {
    var indx = $(this).index();
    $(this).addClass('pro_nav_active').siblings().removeClass('pro_nav_active');
    $('.pro_top_pic1 .pro_list').eq(indx).addClass('p_show').siblings().removeClass('p_show');
  });
  $('.propic2  a').on('mouseenter', function () {
    var indx = $(this).index();
    $(this).addClass('pro_nav_active').siblings().removeClass('pro_nav_active');
    $('.pro_top_pic2 .pro_list').eq(indx).addClass('p_show').siblings().removeClass('p_show');
  })
  // 招聘
  $('.re_con_box>ul').on('click', function () {
    $(this).next().show().parent().siblings().children('.re_con_box1 ').hide();
  })
  //公司简介
  $('.profile_left li').on('click', function () {
    $(this).addClass('pr_active').siblings().removeClass('pr_active');
    $('.pro_r_con').eq($(this).index()).addClass('pr_show').siblings().removeClass('pr_show');
  })

  // 最新贷款信息
  $.fn.myScroll = function (options) {

    var defaults = {
      speed: 40,
      rowHeight: 24
    };

    var opts = $.extend({}, defaults, options),
      intId = [];

    function marquee(obj, step) {

      obj.find("ul").animate({
        marginTop: '-=1'
      }, 0, function () {
        var s = Math.abs(parseInt($(this).css("margin-top")));
        if (s >= step) {
          $(this).find("li").slice(0, 1).appendTo($(this));
          $(this).css("margin-top", 0);
        }
      });
    }

    this.each(function (i) {
      var sh = opts["rowHeight"],
        speed = opts["speed"],
        _this = $(this);
      intId[i] = setInterval(function () {
        if (_this.find("ul").height() <= _this.height()) {
          clearInterval(intId[i]);
        } else {
          marquee(_this, sh);
        }
      }, speed);

      _this.hover(function () {
        clearInterval(intId[i]);
      }, function () {
        intId[i] = setInterval(function () {
          if (_this.find("ul").height() <= _this.height()) {
            clearInterval(intId[i]);
          } else {
            marquee(_this, sh);
          }
        }, speed);
      });

    });

  };

  $("#scroInfo").myScroll({
    speed: 30,
    rowHeight: 60
  });


  //首页 
  $('.section_info').hover(function () {
    $(this).stop().animate({
      marginTop: '-6px'
    }, 200)
  }, function () {
    $(this).stop().animate({
      marginTop: 0
    }, 200)
  });
  // 立即申请上移效果
  $('.apply_btn').hover(function () {
    $(this).stop().animate({
      marginTop: '-3px'
    }, 200)
  }, function () {
    $(this).stop().animate({
      marginTop: 0
    }, 200)
  })

  // 首页产品列表鼠标移入


  // 友情链接
  var f_wid = 0;
  $('.friend_scroll').find('li').each(function (indx, ele) {
    f_wid += $(this).width();
  });
  $('.friend_scroll>ul').width(f_wid);
  var leader = 0;
  // var timer = setInterval(function () {
  //   f_scrl();
  // }, 16)

  // function f_scrl() {
  //   leader -= 1;
  //   var f_fit = $('.friend_scroll li')[0],
  //     m_left = $('.friend_scroll>ul').css('marginLeft');
  //   if (Math.abs(parseInt(m_left)) > $(f_fit).width()) {
  //     $(f_fit).appendTo('.friend_scroll>ul');
  //     leader = 0;
  //   }
  //   $('.friend_scroll>ul').css('marginLeft', leader + 'px');
  // }

  // $('.friend_scroll').on('mouseenter', function () {
  //   clearT();
  // });

  // $('.friend_scroll').on('mouseleave', function () {
  //   setT();
  // })

  // function clearT() {
  //   clearInterval(timer);
  // }

  // function setT() {
  //   clearT();
  //   timer = setInterval(function () {
  //     f_scrl();
  //   }, 16)
  // }
  // 展开友情链接
  $('.friend_more').toggle(function () {
    // clearT();
    $('.friend_scroll>ul').css({
      'marginLeft': '0px',
      'width': '1064px'
    });
    $('.friend_scroll').off('mouseenter').off('mouseleave').css('height', 'auto');
  }, function () {
    $('.friend_scroll>ul').width(f_wid);
    $('.friend_scroll').css('height', '40px');
    // $('.friend_scroll').on('mouseenter', function () {
    //   clearT();
    // });
    // $('.friend_scroll').on('mouseleave', function () {
    //   setT();
    // })
    // setT();
  })

  // 招聘
  $('.recruit_tit li').on('click', function () {
    $(this).addClass('active').siblings().removeClass('active');
    $('.recruit_con_box').eq($(this).index()).show().siblings().hide();
  })

  // 返回顶部
  $('.goTop').hover(function () {
    $(this).find('img').hide();
    $(this).find('p').addClass('active')
  }, function () {
    $(this).find('img').show();
    $(this).find('p').removeClass('active')
  })

  $(window).scroll(function () {
    if ($(window).scrollTop() >= 400) {
      $('.goTop').fadeIn(200);
    } else {
      $('.goTop').fadeOut(300);
    }
  })

  $('.goTop').on('click', function () {
      $('html,body').animate({
        scrollTop: '0px'
      }, 800)
    })


    !(function () {
      var flag = true;
      $(window).scroll(function () {
        if (flag) {
          if ($(window).scrollTop() >= 400) {
            $('.foot').css({
              marginBottom: '90px'
            })
            $('.fixed_footer').fadeIn(200);
          } else {
            $('.fixed_footer').fadeOut(300);
            $('.foot').css({
              marginBottom: '0px'
            })
          }
        }
      })

      $('.fix_close').on('click', function () {
        flag = false;
        $('.foot').css({
          marginBottom: '0px'
        });
        $('.fixed_footer').fadeOut(300);
        return false;
      })
    })();


  // 页码居中
  var gwidth = $('.good_page_box>ul').width() / 2;
  $('.good_page_box>ul').css({
    marginLeft: -gwidth + 'px'
  })

  //限制字符个数
  function overHide(ele, num) {
    $(ele).each(function () {
      var maxwidth = num;
      if ($(this).text().length > maxwidth) {
        $(this).text($(this).text().substring(0, maxwidth));
        $(this).html($(this).html() + '...');
      }
    });
  }
  overHide('.text_overflow_5', 12);


  // 热门产品
  $('.cmore').toggle(function () {
    var list = $(this).parent().find('.hot_clist li');
    var wid = 0;
    $(list).each(function (i, ele) {
      wid += $(ele).width();
    })
    if (wid > 1070) {
      $(this).parent().find('.hot_clist').css({
        height: 'auto',
        overFlow: 'auto'
      })
      $(this).text('关闭 >')
    }
  }, function () {
    $(this).parent().find('.hot_clist').css({
      height: '26px',
      overFlow: 'hidden'
    })
    $(this).text('更多 >')
  })



  function handlePlaceholderForIE() {
    var jq_version = jQuery().jquery;
    jq_version = jq_version.substring(0, jq_version.indexOf('.', 2));

    if (jq_version >= 1.9) {
      jQuery.browser = new Object();
      jQuery.browser.mozilla = /firefox/.test(navigator.userAgent.toLowerCase());
      jQuery.browser.webkit = /webkit/.test(navigator.userAgent.toLowerCase());
      jQuery.browser.opera = /opera/.test(navigator.userAgent.toLowerCase());
      jQuery.browser.msie = /msie/.test(navigator.userAgent.toLowerCase());
    }
    // placeholder attribute for ie7 & ie8
    if ((jq_version < 1.9 && jQuery.browser.msie && jQuery.browser.version.substr(0, 1) <= 9) ||
      (jq_version >= 1.9 && jQuery.browser.msie && !$.support.leadingWhitespace)
    ) {
      // ie7&ie8
      jQuery('input[placeholder], textarea[placeholder]').each(function () {
        var input = jQuery(this);
        jQuery(input).val(input.attr('placeholder'));
        jQuery(input).focus(function () {
          if (input.val() == input.attr('placeholder')) {
            input.val('');
          }
        });
        jQuery(input).blur(function () {
          if (input.val() == '' || input.val() == input.attr('placeholder')) {
            input.val(input.attr('placeholder'));
          }
        });
      });
    }
  }

  handlePlaceholderForIE();


});