<?php defined('C5_EXECUTE') or die("Access Denied.");?>

<?php $area = new GlobalArea("Common content"); $area->display(); ?>
<section class="clear testimonial_wrapper">
			<div class="container">
				<div class="row">
					<div class="col-12 col-md-12 col-lg-6">
						<div class="testmonials_img" data-aos="fade-right">

							<?php $area = new GlobalArea("Testimonial Image"); $area->display(); ?>

						</div>

						</div>
						<div class="col-12 col-md-12 col-lg-6">
							<div class="testmonials_content">
								<?php //$area = new Area("Testimonial"); $area->display($c); ?>
                <?php $area = new GlobalArea("Testimonial"); $area->display(); ?>
							</div>

						</div>

					</div>
				</div>

</section>
<section class="form_wrapper clear">
	<div class="container">
		<div class="row">
			<div class="col-12 col-md-12 col-lg-5">
				<div class="form_content" data-aos="fade-right">
					<?php //$area = new Area("Form Content"); $area->display($c); ?>
					<?php $area = new GlobalArea("Form Content"); $area->display(); ?>
				</div>
			</div>
			<div class="col-12 col-md-12 col-lg-7">
				<div class="form_home" data-aos="fade-left">
					<?php //$area = new Area("Form"); $area->display($c); ?>
					<?php $area = new GlobalArea("Form"); $area->display(); ?>
				</div>
			</div>
		</div>
	</div>
</section>
