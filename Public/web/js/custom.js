$(function(){

 const Url = "https://www2c1.53kf.com/webCompany.php?arg=10217427&kf_sign=Dk1NDMTY4OYyOTE1NzkzNTY4Nzg2MDAzNzIyMTc0Mjc%253D&style=3";
 const Urlform = "/qs";

 var MobileUA = (function() {
        var ua = navigator.userAgent.toLowerCase();

        var mua = {
            IOS: /ipod|iphone|ipad/.test(ua), //iOS
            IPHONE: /iphone/.test(ua), //iPhone
            IPAD: /ipad/.test(ua), //iPad
            ANDROID: /android/.test(ua), //Android Device
            WINDOWS: /windows/.test(ua), //Windows Device
            TOUCH_DEVICE: ('ontouchstart' in window) || /touch/.test(ua), //Touch Device
            MOBILE: /mobile/.test(ua), //Mobile Device (iPad)
            ANDROID_TABLET: false, //Android Tablet
            WINDOWS_TABLET: false, //Windows Tablet
            TABLET: false, //Tablet (iPad, Android, Windows)
            SMART_PHONE: false //Smart Phone (iPhone, Android)
        };

        mua.ANDROID_TABLET = mua.ANDROID && !mua.MOBILE;
        mua.WINDOWS_TABLET = mua.WINDOWS && /tablet/.test(ua);
        mua.TABLET = mua.IPAD || mua.ANDROID_TABLET || mua.WINDOWS_TABLET;
        mua.SMART_PHONE = mua.MOBILE && !mua.TABLET;

        return mua;
    }());

    //SmartPhone
    if (!MobileUA.SMART_PHONE) {
        console.log('mobile');

       
        //头部导航定位
    	$(window).scroll(function() {
    		if($(window).scrollTop()>=100){
    			//$(".header").css({"position":"fixed","top":"0px","left":"0px","width":"100%","z-Index":"13","transition":"all 0.8s"});
    			$(".header").addClass('cur');
    			$(".logo-img").css({"width":"100px","margin-top":"20px"});
    		}else{
    			//$(".header").css({"position":"relative"});
    			$(".header").removeClass('cur');
    			$(".logo-img").css({"width":"auto","margin-top":"0px"});
    		}
    	});
    

    }

    if(MobileUA.SMART_PHONE){
        /* 一级菜单 */
    
        // 展开
        $('.menu-model').on('click', function() {
            $('#mask').css({
                'display': 'block'
            });
            $('.mobile_aside').css({
                'width': '85%',
            });
            $('.m-top-menu').css({
                'display': 'block'
            });

        });
        // 隐藏
        $('.m-top-menu').on('click', function() {
            $('#mask').css({
                'display': 'none'
            });
            $('aside.mobile_aside').css({
                'width': '0'
            });
            $('.m-top-menu').css({
                'display': 'none'
            });
        });
    }  

	$('.xieyi').on('click',function(){
		$('.modal').addClass('show');
	});
	$('#close').on('click',function(){
		$('.modal').removeClass('show');
	})
	$('.pushInput').on('click',function(){
		$('.modal').removeClass('show');
	})
	
	
	
	/* 6.弹出表单框效果 */
	$('.open_bd_box').on('click',function(){
		$('.open-container').css({'transition':'all .3s linear','display':'block'});
		$('.open-tc-box').css({'transition':'all .3s linear','display':'block'});
	})
	$('.open-close').on('click',function(){
		$('.open-container').css({'transition':'all .3s linear','display':'none'});
		$('.open-tc-box').css({'transition':'all .3s linear','display':'none'});
	})
	
	
	/* 1.1视频 */
	$('.videolist').each(function(){ //遍历视频列表
		$(this).hover(function(){ //鼠标移上来后显示播放按钮
			$(this).find('.videoed').show();
		},function(){
			$(this).find('.videoed').hide();
		});
		$(this).click(function(){ //这个视频被点击后执行
			var img = $(this).attr('vpath');//获取视频预览图
			console.log(img)
			var video = $(this).attr('ipath');//获取视频路径
			$('.videos').html("<video id=\"video\" poster='"+img+"' style='width: 640px' src='"+video+"' preload=\"auto\" controls=\"controls\" autoplay=\"autoplay\"></video><img onClick=\"close_video()\" class=\"vclose\" src=\"/statics/huinet/images/close.png\" width=\"25\" height=\"25\"/>");
			$('.videos').show();
		});
	});
	
	function close_video(){
		var v = document.getElementById('video');//获取视频节点
		$('.videos').hide();//点击关闭按钮关闭暂停视频
		v.pause();
		$('.videos').html();
	}

	/* 3.团队左右切换 */
	var flag = "left";
	function DY_scroll(wraper, prev, next, img, speed, or) {
		var wraper = $(wraper);
		var prev = $(prev);
		var next = $(next);
		var img = $(img).find('ul');
		var w = img.find('li').outerWidth(true);
		var s = speed;
		next.click(function() {
			img.animate({
				'margin-left': -w
			},
			function() {
				img.find('li').eq(0).appendTo(img);
				img.css({
					'margin-left': 0
				});
			});
			flag = "left";
		});
		prev.click(function() {
			img.find('li:last').prependTo(img);
			img.css({
				'margin-left': -w
			});
			img.animate({
				'margin-left': 0
			});
			flag = "right";
		});
		if (or == true) {
			ad = setInterval(function() {
				flag == "left" ? next.click() : prev.click()
			},
			s * 1000);
			wraper.hover(function() {
				clearInterval(ad);
			},
			function() {
				ad = setInterval(function() {
					flag == "left" ? next.click() : prev.click()
				},
				s * 1500);
			});
		}
	}
	DY_scroll('.hl_main5_content', '.hl_scrool_leftbtn', '.hl_scrool_rightbtn', '.hl_main5_content1', 3, false); // true为自动播放，不加此参数或false就默认不自动 

	/* 3.1团队hover效果 */
	$('.card-area li').mouseover(function(){
		$('.card-area li').css('width','280px');
		$('.card-area li').removeClass('cur');
		$(this).addClass('cur');
		$(this).css({"width":"364px"});
		
		var n = $(this).index();
		if(n==3){
			}else{	
		}
	})
	$(".card-area li").mouseout(function(){
		
	})
	
	/* 快速贷款 */
	$('.ksdk .menu-list ul li').mouseover(function(){
		var no = $(this).index();
		console.log(no);
		$('.ksdk .menu-list ul li').removeClass('cur');
		$(this).addClass('cur');
		var no = no+1;
		
		$('.ksdk .content-list .loop').removeClass('cur');
		$('.ksdk .content-list .loop:nth-child('+no+')').addClass('cur');
	})


	// 热门产品
	$('.goods-loop .item').mouseover(function(){
		var no = $(this).index();
		$('.goods-loop .item').removeClass('cur');
		$(this).addClass('cur');
		var no = no+1;
		$('.goods-loop .item').removeClass('cur');
		$('.goods-loop .item:nth-child('+no+')').addClass('cur');
	})

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
	
	// Top 关注微信
	$(".gzwx").mouseover(function () {
		$(".top-wx-img").css("display","block");
	})
	$(".gzwx").mouseout(function () {
		$(".top-wx-img").css("display","none");
	})
	
	// 热门产品数字滚动
	$('.count').each(function () {
		(function rec(self, cnt) {
			$(self).prop('Counter',0).animate({
				Counter: cnt
			}, {
				duration: 10000,
				easing: 'swing',
	
				step: function (now) {
					$(self).text(Math.ceil(now));
				},
				complete : function() {
					setTimeout(function() {
					   // rec(self, cnt);
					}, 1000*10);
				}
			});
    	}(this, $(this).text()));
	});

	$(".t53").click(function () {
		window.open(Url);

	});

	$(".tbd").click(function () {

		location.href = Urlform;
	});

})