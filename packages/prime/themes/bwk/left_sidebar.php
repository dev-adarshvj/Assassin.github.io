<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php $this->inc('elements/header.php');?>
<section class="banner clear"><?php $a = new Area('Banner'); $a->display(); ?></section>
<section class="clear left_wrapper">
  <div class="container">
    <div class="row">
      <div class="col-12 col-md-12 col-lg-4">
          <?php $a = new Area('sidebar'); $a->display(); ?>
        </div>
        <div class="col-12 col-md-12 col-lg-8">
          <div class="content ">
            <?php $a = new Area('Main Content'); $a->display(); ?>
          </div>
        </div>
      </div>
    </div>
</section>
<?php $this->inc('elements/sidebar_common.php'); ?>
<?php $this->inc('elements/footer.php'); ?>
