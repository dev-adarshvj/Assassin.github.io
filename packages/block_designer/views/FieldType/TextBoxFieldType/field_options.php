<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="content-field-options">
    <div class="form-group">
        <label for="fields[{{id}}][max_length]" class="control-label">
            <?php echo t('Maximum number of characters'); ?>
            <small><?php echo t('Between %s and %s', 1, 255); ?></small>
        </label>
        <input type="text"
               name="fields[{{id}}][max_length]"
               id="fields[{{id}}][max_length]"
               size="3"
               value="{{max_length}}"
               maxlength="5"
               data-validation-optional="true"
               data-validation="number"
               data-validation-allowing="range[1;255]"
               class="form-control ccm-input-text"/>
    </div>

    <div class="row form-group-fake">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="fields[{{id}}][placeholder]" class="control-label">
                    <?php echo t('Placeholder'); ?>
                </label>
                <input type="text"
                       name="fields[{{id}}][placeholder]"
                       id="fields[{{id}}][placeholder]"
                       size="3"
                       value="{{placeholder}}"
                       class="form-control"/>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="fields[{{id}}][fallback_value]" class="control-label">
                    <?php echo t('Fallback value'); ?>
                </label>
                <input type="text"
                       name="fields[{{id}}][fallback_value]"
                       id="fields[{{id}}][fallback_value]"
                       size="3"
                       value="{{fallback_value}}"
                       class="form-control"/>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="fields[{{id}}][skip_h]" class="control-label">
            <input type="checkbox" name="fields[{{id}}][skip_h]" value="1" id="fields[{{id}}][skip_h]" {{#xif " this.skip_h == '1' " }}checked="checked"{{/xif}}>
            <?php echo t("Skip converting special characters to HTML entities (concrete5's 'h' function)"); ?>
            <br/>
            <small>
                <?php echo t("This makes it possible to enter HTML characters in your text area"); ?>
            </small>
        </label>
    </div>
</div>