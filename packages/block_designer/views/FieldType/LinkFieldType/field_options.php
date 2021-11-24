<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="content-field-options">
    <div class="form-group">
        <label for="fields[{{id}}][class]" class="control-label">
            <?php echo t('Class(es)'); ?>
            <small><?php echo t("Class(es) to be added to your link, i.e. '%s'", 'product-anchor'); ?></small>
        </label>
        <input type="text"
               name="fields[{{id}}][class]"
               id="fields[{{id}}][class]"
               value="{{class}}"
               data-validation-optional="true"
               data-validation="custom"
               data-validation-length="min3"
               data-validation-regexp="^([a-zA-Z]+)([a-zA-Z-0-9_ ]+)$"
               class="form-control"/>
    </div>

    <div class="form-group">
        <label for="fields[{{id}}][hide_title]" class="control-label">
            <input type="checkbox" name="fields[{{id}}][hide_title]" value="1" id="fields[{{id}}][hide_title]" {{#xif " this.hide_title == '1' " }}checked="checked"{{/xif}}>
            <?php echo t('Hide title field'); ?>
            <br/>
            <small>
                <?php echo t('Do not show an extra field, where an alternative (page) title can be entered'); ?>
            </small>
        </label>
    </div>
</div>