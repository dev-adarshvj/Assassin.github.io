<?php defined('C5_EXECUTE') or die("Access Denied.");
if (isset($blockTypes) && !empty($blockTypes)) {
    $ci = Core::make('helper/concrete/urls');
    $li = [];
    foreach ($blockTypes as $blockType) {
        $btIcon = $ci->getBlockTypeIconURL($blockType);
        $btName = $blockType->getBlockTypeName();
        $btHandle = $blockType->getBlockTypeHandle();
        $label = $blockType->installed === true ? '<span class="label label-success">' . t('Installed') . '</span>' : '<span class="label label-danger">' . t('Not Installed') . '</span>';
        $key = $btName;
        if (isset($li[$key])) {
            $i = 0;
            while (isset($li[$key])) {
                $key = $btName . '_' . $i;
                $i++;
            }
        }
        $li[$key] = '<li><a href="' . URL::to('dashboard/blocks/block_designer/config/' . $btHandle) . '"><img src="' . $btIcon . '"> <strong>' . ucFirst($btName) . '</strong> <em>(' . $btHandle . ')</em>&nbsp;&nbsp;' . $label . '</a></li>';
    }
    asort($li); ?>
    <div class="alert alert-info">
        <i class="fa fa-info"></i>
        <?php echo t('Click on a desired block type to load the config into the Block Designer form.'); ?>
    </div>

    <ul class="item-select-list">
        <?php echo implode('', $li); ?>
    </ul>
    <?php
} else {
    ?>
    <div class="alert alert-info">
        <i class="fa fa-info"></i> <?php echo t('There are currently no blocks available with loadable config files.'); ?>
    </div>
    <?php
}