$(function () {
  $('.navbar>ul>li').on('mouseenter', function () {
    $(this).children('a').addClass('nav_active').parent().siblings().children('a').removeClass('nav_active');
    $(this).children('.subnav').stop().show();
  });
  $('.navbar>ul>li').on('mouseleave', function () {
    $(this).children('a').removeClass('nav_active');
    $(this).children('.subnav').stop().hide();    
  });
});