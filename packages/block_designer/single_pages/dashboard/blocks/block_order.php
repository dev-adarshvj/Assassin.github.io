<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="row">
    <div class="col-lg-4">
        <h3><?php echo t("Block order info"); ?></h3>

        <div class="alert alert-info">
            <?php echo t("Order your blocks for each block type set. The order in which you arrange blocks is in which order they will show up while dragging blocks on the front end."); ?>
        </div>
    </div>
    <div class="col-lg-8">
        <?php
        foreach ($blockTypeSets as $btsID => $blockTypeSet) {
            echo '<h3>' . $blockTypeSet['name'] . '</h3>';
            $li = [];
            foreach ($blockTypeSet['blocks'] as $block) {
                $li[] = '<li id="btID_' . $block['btID'] . '" data-btid="' . $block['btID'] . '"><a href="#"><img src="' . $block['icon'] . '" /> ' . $block['name'] . '</a></li>';
            }
            echo '<ul class="item-select-list block-types-sortable" data-btsid="' . $btsID . '">' . implode('', $li) . '</ul>';
        }?>
    </div>
</div>