<?php defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php');
$c = Page::getCurrentPage();
 ?>
<section class="banner clear"><?php $a = new Area('Banner'); $a->display(); ?></section>
<?php $a = new Area('Main');$a->display($c); ?>

<?php $a = new Area('Main Content');
$a->enableGridContainer();
$blocks = $a->getTotalBlocksInArea($c);?>
<?php if($blocks > 0){ ?>
    <?php if (!$c->isEditMode()) { ?>
<section class="sub_pages">
<?php } ?>
<?php $a->display($c); ?>
 <?php if (!$c->isEditMode()) { ?>
</section>
<?php } ?>
<? }?>
<?php $this->inc('elements/common.php'); ?>
<?php $this->inc('elements/footer.php'); ?>
