<?php defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header_top.php');?>

<div class="mob_menu_wrap d-block d-lg-none">
  <span class="close_popup"></span>
<div class="mobile_pop_up">
  <?php $stack = Stack::getByName('Mobile Menu'); $stack->display(); ?>
</div>
</div>

 <header class="clear">
      <div class="container">
        <div class="row">
          <div class="col-12 col-md-12">
            <div class="site_logo">
             <?php $a = new GlobalArea('Logo'); $a->display(); ?>
            </div>
            <div class="site_logo_top">
             <a href="/"><img src="<?php echo $view->getThemePath()?>/images/logo_top.png" alt="Barton Walter Krier" title="Barton Walter Krier"></a>
            </div>
            <div class="hamburger_menu">
             <span></span>
            </div>
            <div class="menu_wrapper">


              <div class="menu_top_wrapper">

                <div class="menu_social">
                  <?php $a = new GlobalArea('Header Social Icons'); $a->display(); ?>
                </div>

                <div class="menu_top">
                  <?php $a = new GlobalArea('Header Top Menu'); $a->display(); ?>
                </div>

              </div>
              <div class="mainnav">
                <?php $a = new GlobalArea('Header Nav'); $a->display(); ?>
              </div>

            </div>

          </div>

        </div>

      </div>



    </header>


    <header class="clear fixed-top">
         <div class="container">
           <div class="row">
             <div class="col-12 col-md-12">
               <div class="site_logo">
                <?php $a = new GlobalArea('Logo'); $a->display(); ?>
               </div>
               <div class="site_logo_top">
                <a href="/"><img src="<?php echo $view->getThemePath()?>/images/logo_top.png" alt="Barton Walter Krier" title="Barton Walter Krier"></a>
               </div>
               <div class="hamburger_menu">
                <span></span>
               </div>
               <div class="menu_wrapper">


                 <div class="menu_top_wrapper">

                   <div class="menu_social">
                     <?php $a = new GlobalArea('Header Social Icons'); $a->display(); ?>
                   </div>

                   <div class="menu_top">
                     <?php $a = new GlobalArea('Header Top Menu'); $a->display(); ?>
                   </div>

                 </div>
                 <div class="mainnav">
                   <?php $a = new GlobalArea('Header Nav'); $a->display(); ?>
                 </div>

               </div>

             </div>

           </div>

         </div>
   </header>


<script>

$('.hamburger_menu').click(function(){
    $('.mob_menu_wrap').addClass('open');
    $('body').addClass('hum-br-open');
  //  $('.hamburger_menu').toggleClass('open');
  });
  $('.close_popup').click(function(){
    $('.mob_menu_wrap').removeClass('open');
    $('body').removeClass('hum-br-open');
  //  $('.hamburger_menu').removeClass('open');
  });

   $(window).load(function(){
 if($('.footer-sub-nav li').hasClass('nav-path-selected')){
 $('.navbar-nav li.nav-path-selected').addClass('open_nav_footer');}
 });

 $('li > span.has_sub_footer').click(function(e) {
   //  $(".footer-nav-link ul li").removeClass("open_nav_footer");
     $(this).parent().toggleClass("open_nav_footer");
 });

 $(window).load(function(){
if($('.footer-sub-nav-m li').hasClass('nav-path-selected')){
$('.navbar-nav-mob li.nav-path-selected').addClass('open_nav_footer_m');}
});

$('li > span.has_sub_footer_nav').click(function(e) {
 //  $(".footer-nav-link ul li").removeClass("open_nav_footer");
   $(this).parent().toggleClass("open_nav_footer_m");
});
 </script>
