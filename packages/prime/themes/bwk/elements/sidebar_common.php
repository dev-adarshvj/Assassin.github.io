<?php defined('C5_EXECUTE') or die("Access Denied.");?>
<?php $area = new GlobalArea("Common Sidebar Content"); $area->display(); ?>
<section class="clear testimonial_wrapper">
	<div class="container">
		<div class="row">
			<div class="col-12 col-md-12 col-lg-6">
				<div class="testmonials_img" data-aos="fade-right">
					<?php $a = new GlobalArea("Testimonial Image"); $a->display(); ?>

				</div>
			</div>
			<div class="col-12 col-md-12 col-lg-6">
				<div class="testmonials_content">
					<?php $a = new GlobalArea("Testimonial"); $a->display(); ?>
				</div>
			</div>
		</div>
		<div class="knowledge" data-aos="fade-up">
			<?php $a = new GlobalArea("Knowledge"); $a->display(); ?>
		</div>

	</div>

	 <div class="Industries_wrap">
		 <div class="row">
			 <div class="col-12 col-md-12">
				 <?php $a = new GlobalArea("Industries"); $a->display(); ?>
			 </div>
		 </div>
	 </div>



</section>
<section class="form_wrapper clear">
	<div class="container">
		<div class="row">
			<div class="col-12 col-md-12 col-lg-5">
				<div class="form_content" data-aos="fade-up">
					<?php $a = new GlobalArea("Form Content"); $a->display(); ?>
				</div>
			</div>
			<div class="col-12 col-md-12 col-lg-7">
				<div class="form_home" data-aos="fade-down">
					<?php $a = new GlobalArea("Form"); $a->display(); ?>
				</div>
			</div>
		</div>
	</div>
</section>
