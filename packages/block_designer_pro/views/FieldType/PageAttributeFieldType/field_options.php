<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="content-field-options">
    <div class="form-group">
        <label for="fields[{{id}}][attribute]" class="control-label">
            <?php echo t('Attribute'); ?>
            <span class="required">*</span>
        </label>

        <select name="fields[{{id}}][attribute]" class="form-control attribute" id="fields[{{id}}][attribute]" data-validation="required">
            {{#select attribute}}
            <option value="">-- <?php echo t('Select'); ?> --</option><?php
            foreach ($pageAttributes as $k => $v) {
                ?>
                <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
                <?php
            } ?>
            {{/select}}
        </select>
    </div>

    <div class="form-group attribute-other hidden">
        <label for="fields[{{id}}][attribute_other]" class="control-label">
            <?php echo t('Attribute handle'); ?>
            <span class="required">*</span>
        </label>

        <input type="text"
               name="fields[{{id}}][attribute_other]"
               id="fields[{{id}}][attribute_other]"
               value="{{attribute_other}}"
               class="form-control ccm-input-text"
               data-validation-optional="true"
               data-validation="custom"
               data-validation-regexp="^([a-zA-Z0-9]+)([a-zA-Z_0-9]+)$"
            />
    </div>

    <div class="form-group">
        <label for="fields[{{id}}][current_page]" class="control-label">
            <input type="checkbox" name="fields[{{id}}][current_page]" class="current-page" value="1" id="fields[{{id}}][current_page]" {{#xif " this.current_page == '1' " }}checked="checked"{{/xif}}>
            <?php echo t('Use attribute for the current page'); ?>
        </label>
    </div>
</div>