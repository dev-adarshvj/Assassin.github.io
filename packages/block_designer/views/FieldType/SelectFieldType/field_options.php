<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="content-field-options">
    <div class="form-group">
        <label for="fields[{{id}}][select_options]" class="control-label">
            <?php echo t('Select choices (one per line)'); ?>
            <span class="required">*</span>

            <small><?php echo t("Set your own array key for values, by using 2 colons (' :: ') on each line - extra spaces required"); ?></small>
        </label>

        <div class="alert alert-info">
            <b>concrete5_old</b> :: Concrete5 CMS 5.6<br/>
            <b>concrete5</b> :: Concrete5 CMS 5.7<br/>
            <b>wordpress</b> :: WordPress<br/>
            <?php echo t('Value without a key, this will be assigned by the field type'); ?>
        </div>

        <textarea
            rows="3"
            data-validation="required"
            name="fields[{{id}}][select_options]"
            id="fields[{{id}}][select_options]"
            class="form-control">{{select_options}}</textarea>
    </div>

    <div class="form-group">
        <label for="fields[{{id}}][translate]" class="control-label">
            <input type="checkbox" name="fields[{{id}}][translate]" value="1" id="fields[{{id}}][translate]" {{#xif " this.translate == '1' " }}checked="checked"{{/xif}}>
            <?php echo t("Use concrete5's translate function for all select choices (values)"); ?>
        </label>
    </div>

    <div class="form-group">
        <label for="fields[{{id}}][view_output]" class="control-label">
            <?php echo t('View output'); ?>
        </label>

        <select name="fields[{{id}}][view_output]" class="form-control" id="fields[{{id}}][view_output]">
            {{#select view_output}}
            <option value=""><?php echo t('Build PHP switch, I will code the rest myself'); ?></option>
            <option value="1"><?php echo t('Echo the selected key, i.e. %s', 'concrete5_old'); ?></option>
            <option value="2"><?php echo t('Echo the selected value, i.e. %s', 'Concrete5 CMS 5.6'); ?></option>
            {{/select}}
        </select>
    </div>

    <div class="form-group">
        <label for="fields[{{id}}][default_value]" class="control-label">
            <?php echo t('Default Value'); ?>

            <small>
                <?php echo t("Enter the array key in this field, in case you want to set a default value for this field (only for newly added blocks/items)"); ?>
            </small>
        </label>

        <input type="text"
               name="fields[{{id}}][default_value]"
               id="fields[{{id}}][default_value]"
               value="{{default_value}}"
               class="form-control"/>
    </div>
</div>