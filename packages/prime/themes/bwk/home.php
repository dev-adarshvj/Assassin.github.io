<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php $this->inc('elements/header.php');?>
<section class="clear slider">
	<?php $area = new Area("Slider"); $area->display($c); ?>
</section>
<section class="clear home_block_wrapper">
	<div class="container">
		<?php $area = new Area("Home Highlight"); $area->display($c); ?>
	</div>
</section>
<section class="clear testimonial_wrapper">
	<div class="container">
		<div class="row">
			<div class="col-12 col-md-12 col-lg-6">
				<div class="testmonials_img"  data-aos="fade-right">
					<?php $area = new GlobalArea("Testimonial Image"); $area->display($c); ?>
				</div>
			</div>
			<div class="col-12 col-md-12 col-lg-6">
				<div class="testmonials_content">
					<?php $area = new GlobalArea("Testimonial"); $area->display($c); ?>
				</div>
			</div>
		</div>
		<div class="knowledge">
			<?php $area = new Area("Knowledge"); $area->display($c); ?>
		</div>

	</div>

	 <div class="Industries_wrap">
		 <div class="row">
			 <div class="col-12 col-md-12">
				 <?php $area = new Area("Industries"); $area->display($c); ?>
			 </div>
		 </div>
	 </div>



</section>
<section class="form_wrapper clear">
	<div class="container">
		<div class="row">
			<div class="col-12 col-md-12 col-lg-5">
				<div class="form_content" data-aos="fade-right">
					<?php $area = new GlobalArea("Form Content"); $area->display($c); ?>
				</div>
			</div>
			<div class="col-12 col-md-12 col-lg-7">
				<div class="form_home"  data-aos="fade-left">
					<?php $area = new GlobalArea("Form"); $area->display($c); ?>
				</div>
			</div>
		</div>
	</div>
</section>
<section class="clear news">
	<div class="container">
		<div class="row">
			<div class="col-12 col-md-12 col-lg-5">
				<div class="news-img"  data-aos="fade-right">
					<?php $area = new Area("Insights Image"); $area->display($c); ?>
				</div>
			</div>
			<div class="col-12 col-md-12 col-lg-7">
				<div class="news-txt" data-aos="fade-up">
					<?php $area = new Area("Insights"); $area->display($c); ?>
				</div>
			</div>
		</div>

			<?php $area = new Area("Blog"); $area->display($c); ?>

	</div>
</section>
		<?php $this->inc('elements/footer.php'); ?>
