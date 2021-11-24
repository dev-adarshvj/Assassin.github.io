<?php defined('C5_EXECUTE') or die("Access Denied.");
use \Concrete\Core\Block\BlockController;
use \Concrete\Core\Editor\LinkAbstractor;
$this->inc('elements/header.php');?>
<?php $cp=Page::getByID($c->cParentID);?>
<section class="banner clear"> <?php $a = new GlobalArea('Banner'); $a->display(); ?></section>
     <section class="clear left_wrapper">
    <div class="inside-highlights clear">
     
        <div class="container inside-container-pad">
        <h3><?php echo $c->getCollectionName(); ?></h3>
        <?php echo LinkAbstractor::translateFrom(LinkAbstractor::translateTo($c->getAttribute('service_content')));?>
        </div>
    </div>
    </section>
<?php  $this->inc('elements/footer.php'); ?>