<?php defined('C5_EXECUTE') or die("Access Denied.");
$themePath = $view->getThemePath();

?>
<!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php
     View::element('header_required', [
         'pageTitle' => isset($pageTitle) ? $pageTitle : '',
         'pageDescription' => isset($pageDescription) ? $pageDescription : '',
         'pageMetaKeywords' => isset($pageMetaKeywords) ? $pageMetaKeywords : ''
     ]);
    ?>
  <link rel="stylesheet" href="https://use.typekit.net/cdp7czr.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <!-- <link rel="stylesheet/less" type="text/css" href="<?php //echo $themePath;?>/css/styles.less" /> -->
  <?php echo $html->css($view->getStylesheet('styles.less'));?>
  <link href="<?php echo $view->getThemePath()?>/css/meanmenu.css" rel="stylesheet">
  <link href="<?php echo $view->getThemePath()?>/css/mobilemenubutton.css" rel="stylesheet">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="<?php echo $themePath;?>/slick/slick.css"/>
  <link rel="stylesheet" type="text/css" href="<?php echo $themePath;?>/slick/slick-theme.css"/>
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/> -->
  

  <script type="text/javascript" src="<?php echo $this->getThemePath(); ?>/js/lib/tether.min.js"></script>
  <script type="text/javascript" src="<?php echo $this->getThemePath(); ?>/js/jquery.bootpag.min.js"></script>
  <script src="<?php echo $view->getThemePath()?>/js/jquery.meanmenu.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script type="text/javascript" src="//code.jquery.com/jquery-1.11.0.min.js"></script>
  <script src="//cdn.jsdelivr.net/npm/less"></script>
  <script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
  <script type="text/javascript" src="<?php echo $this->getThemePath(); ?>/slick/slick.min.js"></script>


  <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script> -->
              <!-- <script>
              new WOW().init();
              </script> -->
              <?php if(!$c->isEditMode()) {
?>
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="<?php echo $themePath;?>/css/animation.css" />
  <script>


      jQuery(function($) {

        // Function which adds the 'animated' class to any '.animatable' in view
        var doAnimations = function() {

          // Calc current offset and get all animatables
          var offset = $(window).scrollTop() + $(window).height(),
              $animatables = $('.animatable');

          // Unbind scroll handler if we have no animatables
          if ($animatables.length == 0) {
            $(window).off('scroll', doAnimations);
          }

          // Check all animatables and animate them if necessary
      		$animatables.each(function(i) {
             var $animatable = $(this);
      			if (($animatable.offset().top + $animatable.height() - 20) < offset) {
              $animatable.removeClass('animatable').addClass('animated');
      			}
          });

      	};

        // Hook doAnimations on scroll, and trigger a scroll
      	$(window).on('scroll', doAnimations);
        $(window).trigger('scroll');

      });
  </script>
  <?php
}
?>
</head>
<body>
  <div class="<?php echo $c->getPageWrapperClass();?> wrapper">
