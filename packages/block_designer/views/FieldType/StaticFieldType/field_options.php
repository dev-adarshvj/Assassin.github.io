<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="form-group">
    <label for="fields[{{id}}][static_html]" class="control-label">
        <?php echo t('Static HTML'); ?>
        <small><?php echo t('Anything entered here will be directly outputted to the block view. Users will not be able to edit it.'); ?></small>
    </label>

    <textarea
        rows="3"
        name="fields[{{id}}][static_html]"
        id="fields[{{id}}][static_html]"
        class="form-control">{{static_html}}</textarea>
</div>