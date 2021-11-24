<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="alert alert-info" style="margin-top: 20px;">
    <i class="fa fa-info"></i>
    <?php echo t("Download and get started using Font Awesome by visiting the Font Awesome '<a href='%s' target='_blank'>Get Started</a>' page.", 'http://fortawesome.github.io/Font-Awesome/get-started/'); ?>
</div>

<div class="content-field-options">
    <div class="form-group">
        <label for="fields[{{id}}][version]" class="control-label">
            <?php echo t('Version'); ?>
        </label>

        <select name="fields[{{id}}][version]" class="form-control" id="fields[{{id}}][version]">
            {{#select version}}
            <option value="4-2"><?php echo t('Version'); ?> 4.2</option>
            <option value="4-3"><?php echo t('Version'); ?> 4.3</option>
            <option value="4-4"><?php echo t('Version'); ?> 4.4</option>
            <option value="4-5"><?php echo t('Version'); ?> 4.5</option>
            <option value="4-6"><?php echo t('Version'); ?> 4.6</option>
            <option value="4-7"><?php echo t('Version'); ?> 4.7</option>
            {{/select}}
        </select>
    </div>

    <div class="form-group">
        <label for="fields[{{id}}][show_preview]" class="control-label">
            <input type="checkbox" name="fields[{{id}}][show_preview]" value="1" id="fields[{{id}}][show_preview]" {{#xif " this.show_preview == '1' " }}checked="checked"{{/xif}}>
            <?php echo t('Show preview of the icon'); ?>
        </label>
    </div>
</div>