$(function () {

  var mySwiper = $('.swiper-container').swiper({
    loop: true,
    autoplay: 2000,
    pagination: '.pagination',
    paginationClickable :true,
  });

  var mySwiper1 = $('.swiper-container1').swiper({
    loop: true,
    autoplay: 2000,
    pagination: '.pagination1',
    paginationClickable :true,
  });


  var mySwiper4 = $('.swiper-container4').swiper({
    loop: true,
    // autoplay: 2000,
    pagination: '.pagination4',
    paginationClickable :true,
  });

  var mySwiper2 = new Swiper('.swiper-container2',{
    paginationClickable: true,
    autoplay: 2000,    
    loop: true,    
    slidesPerView: 4
  });
  $('.arrow-left').on('click', function(e){
    e.preventDefault()
    mySwiper2.swipePrev()
  });
  $('.arrow-right').on('click', function(e){
    e.preventDefault();
    mySwiper2.swipeNext()
  });


  $('.dkfa').hover(function () {
    $(this).find('span').stop().animate({
      right: 32 + 'px'
    })
  },function () {
    $(this).find('span').stop().animate({
      right: 14 + 'px'
    })
  });

  $('.nat_main_list').hover(function () {
    $(this).stop().animate({
      marginLeft: '10px'
    }, 200)
  }, function () {
    $(this).stop().animate({
      marginLeft: 0
    }, 200)
  })

});