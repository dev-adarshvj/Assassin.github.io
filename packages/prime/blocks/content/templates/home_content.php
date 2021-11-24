<?php
    defined('C5_EXECUTE') or die("Access Denied.");
    $c = Page::getCurrentPage();
    if (!$content && is_object($c) && $c->isEditMode()) {
        ?>
		<div class="ccm-edit-mode-disabled-item"><?=t('Empty Content Block.')?></div> 
	<?php 
    } else { ?>
    	<div class="col-12 col-md-7">
						<div class="home_block_box">
    <?php    echo $content; ?>
</div>
</div>
    <?php
    } ?>
