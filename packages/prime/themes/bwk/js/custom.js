$(document).ready(function () {
$(".main-backtop").css({display:"block"});
$(".main-backtop").hide();
$(function(){
	$(window).scroll(function(){
		$(this).scrollTop()>200?$(".main-backtop").fadeIn():$(".main-backtop").fadeOut();
	$(this).scrollTop()>500?$(".main-backtop").addClass("is-fade-out"):$(".main-backtop").removeClass("is-fade-out");
});
	$(".main-backtop").on("click",function(){ return $("body,html").animate({scrollTop:0},1e3),!1 });
});

if($('#cyear').length){ document.getElementById("cyear").innerHTML = new Date().getFullYear(); }

if($('.mobile_nav').length){ $('.mobile_nav').click(function(){	$('.menu_popup').addClass('open'); }); }
if($('.popup_close').length){  $('.popup_close').click(function(){ $('.menu_popup').removeClass('open'); });}
    $(".down-arrow").on("click",function(){ $('html,body').animate({ scrollTop: $(".main_highlights").offset().top - 143}, 1000); });
});


