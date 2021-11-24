<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="content-field-options">
    <div class="row">
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

    <div class="row" style="margin-top: 10px;">
        <div class="col-md-6">
            <div class="form-group">
                <label for="fields[{{id}}][database_length]" class="control-label">
                    <?php echo t('Database length'); ?>
                    <small><?php echo t('The maximum length of the number'); ?></small>
                </label>
                <input type="text"
                       name="fields[{{id}}][database_length]"
                       id="fields[{{id}}][database_length]"
                       value="{{database_length}}"
                       placeholder="10"
                       data-validation-optional="true"
                       data-validation="number"
                       data-validation-allowing="range[-1;10485760],negative"
                       class="form-control database_decimals"/>
            </div>

            <div class="form-group">
                <label for="fields[{{id}}][database_decimals]" class="control-label">
                    <?php echo t('Database decimals'); ?>
                    <small><?php echo t('How many decimals the number may have'); ?></small>
                </label>
                <input type="text"
                       name="fields[{{id}}][database_decimals]"
                       id="fields[{{id}}][database_decimals]"
                       value="{{database_decimals}}"
                       placeholder="2"
                       data-validation-optional="true"
                       data-validation="number"
                       data-validation-allowing="range[-53;53],negative"
                       class="form-control database_decimals"/>
            </div>

            <div class="form-group">
                <label for="fields[{{id}}][disallow_float]" class="control-label">
                    <input class="disallow_float" type="checkbox" name="fields[{{id}}][disallow_float]" value="1" id="fields[{{id}}][disallow_float]" {{#xif " this.disallow_float == '1' " }}checked="checked"{{/xif}}>
                    <?php echo t('Disallow float number'); ?>
                </label>
            </div>

            <div class="form-group">
                <label for="fields[{{id}}][number_format]" class="control-label">
                    <input class="number_format" type="checkbox" name="fields[{{id}}][number_format]" value="1" id="fields[{{id}}][number_format]" {{#xif " this.number_format == '1' " }}checked="checked"{{/xif}}>
                    <?php echo t('Number format'); ?>
                </label>
            </div>

            <div class="form-group-fake number-formatting hidden">
                <div class="form-group">
                    <label for="fields[{{id}}][number_format_decimals]" class="control-label">
                        <?php echo t('Decimals'); ?>
                        <small><?php echo t('Sets the number of decimal points.'); ?></small>
                    </label>
                    <input type="text"
                           name="fields[{{id}}][number_format_decimals]"
                           id="fields[{{id}}][number_format_decimals]"
                           value="{{number_format_decimals}}"
                           placeholder="0"
                           data-validation-optional="true"
                           data-validation="number"
                           class="form-control"/>
                </div>
                <div class="form-group">
                    <label for="fields[{{id}}][number_format_decimal_point]" class="control-label">
                        <?php echo t('Decimal point'); ?>
                        <small><?php echo t('Sets the separator for the decimal point.'); ?></small>
                    </label>
                    <input type="text"
                           name="fields[{{id}}][number_format_decimal_point]"
                           id="fields[{{id}}][number_format_decimal_point]"
                           value="{{number_format_decimal_point}}"
                           placeholder="."
                           maxlength="1"
                           data-validation-optional="true"
                           data-validation="length"
                           data-validation-length="max1"
                           class="form-control"/>
                </div>
                <div class="form-group">
                    <label for="fields[{{id}}][number_format_thousand_sep]" class="control-label">
                        <?php echo t('Thousands separator'); ?>
                        <small><?php echo t('Sets the thousands separator.'); ?></small>
                    </label>
                    <input type="text"
                           name="fields[{{id}}][number_format_thousand_sep]"
                           id="fields[{{id}}][number_format_thousand_sep]"
                           value="{{number_format_thousand_sep}}"
                           placeholder=","
                           maxlength="1"
                           data-validation-optional="true"
                           data-validation="length"
                           data-validation-length="max1"
                           class="form-control"/>
                </div>
            </div>
        </div>

        <div class="col-md-6 side-xs-xs side-sm-xs">
            <div class="form-group">
                <label for="fields[{{id}}][min_number]" class="control-label">
                    <?php echo t('Minimum number'); ?>
                    <small><?php echo t('Use a dot (.) as a decimal separator'); ?></small>
                </label>
                <input type="text"
                       name="fields[{{id}}][min_number]"
                       id="fields[{{id}}][min_number]"
                       value="{{min_number}}"
                       data-validation-optional="true"
                       data-validation="number"
                       data-validation-allowing="float,negative"
                       class="form-control min_number"/>
            </div>
            <div class="form-group">
                <label for="fields[{{id}}][max_number]" class="control-label">
                    <?php echo t('Maximum number'); ?>
                </label>
                <input type="text"
                       name="fields[{{id}}][max_number]"
                       id="fields[{{id}}][max_number]"
                       value="{{max_number}}"
                       data-validation-optional="true"
                       data-validation="number"
                       data-validation-allowing="float,negative"
                       class="form-control max_number"/>
            </div>

            <div class="alert alert-warning hidden alert-floats" style="margin-top:15px;">
                <?php echo t("You can not enter floating numbers on the fields above, as the box '%s' has been checked.", t('Disallow float number')); ?>
            </div>
        </div>
    </div>
</div>

