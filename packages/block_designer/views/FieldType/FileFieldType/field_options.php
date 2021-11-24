<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="content-field-options">
    <div class="form-group">
        <label for="fields[{{id}}][link_class]" class="control-label">
            <?php echo t('Class(es)'); ?>
            <small><?php echo t("Class(es) to be added to your file, i.e. '%s'", 'file-anchor'); ?></small>
        </label>

        <input type="text"
               name="fields[{{id}}][link_class]"
               id="fields[{{id}}][link_class]"
               value="{{link_class}}"
               data-validation-optional="true"
               data-validation="custom"
               data-validation-length="min3"
               data-validation-regexp="^([a-zA-Z]+)([a-zA-Z-_ ]+)$"
               class="form-control"/>
    </div>

    <div class="form-group">
        <label for="fields[{{id}}][download]" class="control-label">
            <input type="checkbox" name="fields[{{id}}][download]" value="1" id="fields[{{id}}][download]" {{#xif " this.download == '1' " }}checked="checked"{{/xif}}>
            <?php echo t('Use download link, instead of relative path link'); ?>
        </label>
    </div>

    <div class="form-group">
        <input type="checkbox" name="fields[{{id}}][url_target]" value="1" id="fields[{{id}}][url_target]" {{#xif " this.url_target == '1' " }}checked="checked"{{/xif}}>
        <label for="fields[{{id}}][url_target]" class="control-label"><?php echo t('Link opens in a new window'); ?></label>
    </div>

    <div class="form-group">
        <label for="fields[{{id}}][title_field]" class="control-label">
            <input type="checkbox" class="title_field" name="fields[{{id}}][title_field]" value="1" id="fields[{{id}}][title_field]" {{#xif " this.title_field == '1' " }}checked="checked"{{/xif}}>
            <?php echo t('Show title field'); ?>
        </label>
    </div>

    <div class="form-group-fake title_field-values hidden">
        <label for="fields[{{id}}][title_field_required]" class="control-label">
            <input type="checkbox" class="title_field_required" name="fields[{{id}}][title_field_required]" value="1" id="fields[{{id}}][title_field_required]" {{#xif " this.title_field_required == '1' " }}checked="checked"{{/xif}}>
            <?php echo t('Required title field?'); ?>
        </label>

        <div class="form-group">
            <label for="fields[{{id}}][title_field_placeholder]" class="control-label">
                <?php echo t('Title placeholder'); ?>
            </label>
            <input type="text"
                   name="fields[{{id}}][title_field_placeholder]"
                   id="fields[{{id}}][title_field_placeholder]"
                   size="3"
                   value="{{title_field_placeholder}}"
                   class="form-control"/>
        </div>
        <div class="form-group">
            <label for="fields[{{id}}][title_field_fallback_value]" class="control-label">
                <?php echo t('Title fallback value'); ?>
                <small><?php echo t('If left blank and the user does not fill in a title, the name of the file will be used as title'); ?></small>
            </label>
            <input type="text"
                   name="fields[{{id}}][title_field_fallback_value]"
                   id="fields[{{id}}][title_field_fallback_value]"
                   size="3"
                   value="{{title_field_fallback_value}}"
                   class="form-control title_field_fallback_value"/>
        </div>
    </div>
</div>