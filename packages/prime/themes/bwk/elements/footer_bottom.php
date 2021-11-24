<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
</div>


<?php if (!($c->isEditMode())) { ?>

 <script src="<?php echo $this->getThemePath() ?>/js/bootstrap.min.js"></script>
<?php } ?>

<script type="text/javascript">
  $( document ).ready(function() {

    $('.home-slider').slick({
      slidesToShow: 1,
      slidesToScroll: 1,
      autoplay: true,
      autoplaySpeed: 7000,
      dots: true,
      fade: true,
      speed: 500,
        customPaging : function(slider, i) {
            var thumb = jQuery(slider.$slides[i]).data();
            return '<a>'+('0'+(i+1)).slice(-2)+'</a>';
        }
});
    $('.testimonial-slider').slick({
      slidesToShow: 1,
      slidesToScroll: 1,
       arrows: false,
       autoplay: true,
       autoplaySpeed: 2000
    });
    $('.industries-slider').slick({
      slidesToShow: 1,
      arrows: false,
      slidesToScroll: 1,
      dots: true,
        customPaging : function(slider, i) {
            var thumb = jQuery(slider.$slides[i]).data();
            return '<a>'+('0'+(i+1)).slice(-2)+'</a>';
        }
    });
    $('.service_highlight_slider').slick({
      slidesToShow: 4,
      slidesToScroll: 4,
      arrows: false,
      infinite: true,
      responsive: [
    {
      breakpoint: 1199,
      settings: {
        slidesToShow: 3,
        slidesToScroll: 3,
        infinite: true,
        dots: true
      }
    },
    {
      breakpoint: 991,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 2
      }
    },
    {
      breakpoint: 767,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1
      }
    }
    // You can unslick at a given breakpoint now by adding:
    // settings: "unslick"
    // instead of a settings object
  ],
       //autoplay: true,
      // autoplaySpeed: 2000,
      dots: true,
        customPaging : function(slider, i) {
            var thumb = jQuery(slider.$slides[i]).data();
            return '<a>'+('0'+(i+1)).slice(-2)+'</a>';
        }
});
    $(window).scroll(function() {

    var scroll = $(window).scrollTop();

    if (scroll >= 200) {
        $(".fixed-top").addClass("sticky-nav");
      //  $(".site_logo_top").show();
    } else {
    //  $(".site_logo_top").hide();
      $(".fixed-top").removeClass("sticky-nav");
    }
});
document.getElementById("year").innerHTML = new Date().getFullYear();

});

</script>
<script>
AOS.init();
</script>

<?php if ($c->isEditMode()) { ?>
<style>

#ccm-theme-grid-edit-mode-row-wrapper {
   position: relative;
   display: -ms-flexbox;
   display: flex;
   -ms-flex-wrap: wrap;
   flex-wrap: wrap;
   flex-grow: 1;
}
</style>
<?php } ?>
<?php View::element('footer_required'); ?>

</body>
</html>
