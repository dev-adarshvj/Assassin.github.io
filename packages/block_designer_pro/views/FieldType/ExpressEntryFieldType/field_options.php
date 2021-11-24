<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="content-field-options">
    <div class="alert alert-info">
        <p>
            <?php echo t("Using this field, you are able to select an entry from the Express Entity. This field type needs custom scripting though! It will generate some code in the view, just so you know where you can place your custom code and which variable(s) to use."); ?>
        </p>
        <p>
            <?php echo t("%s can be managed <a href='%s' target='_blank'>here</a>.", t('Express Data Objects'), URL::to('/dashboard/system/express/entities')); ?>
        </p>
    </div>

    <div class="form-group">
        <label for="fields[{{id}}][entity_handle]" class="control-label">
            <?php echo t('Entity handle'); ?>
            <span class="required">*</span>
        </label>

        <input type="text"
               name="fields[{{id}}][entity_handle]"
               id="fields[{{id}}][entity_handle]"
               value="{{entity_handle}}"
               class="form-control ccm-input-text"
               data-validation="custom"
               data-validation-regexp="^([a-zA-Z0-9]+)([a-zA-Z_0-9]+)$"/>
    </div>
</div>