<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="content-field-options">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="fields[{{id}}][min_length]" class="control-label">
                    <?php echo t('Minimum stacks'); ?>
                </label>

                <input type="text"
                       name="fields[{{id}}][min_length]"
                       id="fields[{{id}}][min_length]"
                       size="3"
                       value="{{min_length}}"
                       data-validation-optional="true"
                       data-validation="number"
                       data-validation-allowing="range[1;999]"
                       class="form-control ccm-input-text"/>
            </div>
        </div>

        <div class="col-sm-6 side-xs-xs">
            <div class="form-group">
                <label for="fields[{{id}}][max_length]" class="control-label">
                    <?php echo t('Maximum stacks'); ?>
                </label>

                <input type="text"
                       name="fields[{{id}}][max_length]"
                       id="fields[{{id}}][max_length]"
                       size="3"
                       value="{{max_length}}"
                       data-validation-optional="true"
                       data-validation="number"
                       data-validation-allowing="range[1;999]"
                       class="form-control ccm-input-text"/>
            </div>
        </div>
    </div>
</div>