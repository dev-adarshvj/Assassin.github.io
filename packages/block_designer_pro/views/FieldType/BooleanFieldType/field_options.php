<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="content-field-options">
    <div class="form-group">
        <label for="fields[{{id}}][fallback_value]" class="control-label">
            <?php echo t('Fallback value'); ?>
        </label>

        <select name="fields[{{id}}][fallback_value]" class="form-control" id="fields[{{id}}][fallback_value]">
            {{#select fallback_value}}
            <option value="">-- <?php echo t('None'); ?> --</option>
            <option value="0"><?php echo t('No'); ?></option>
            <option value="1"><?php echo t('Yes'); ?></option>
            {{/select}}
        </select>
    </div>

    <div class="form-group">
        <label for="fields[{{id}}][default_value]" class="control-label">
            <?php echo t('Default value'); ?>
        </label>

        <select name="fields[{{id}}][default_value]" class="form-control" id="fields[{{id}}][default_value]">
            {{#select default_value}}
            <option value="">-- <?php echo t('None'); ?> --</option>
            <option value="0"><?php echo t('No'); ?></option>
            <option value="1"><?php echo t('Yes'); ?></option>
            {{/select}}
        </select>
    </div>

    <div class="alert alert-info" style="margin: 10px 0;">
        <?php echo t("Only fill in these values if you want to replace the standard '%s'/'%s' language labels or values.", t('Yes'), t('No')); ?>
    </div>

    <div class="row"><?php
        $yesNo = [
            'yes' => 'Yes',
            'no'  => 'No',
        ];
        foreach ($yesNo as $k => $v) {
            ?>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="fields[{{id}}][<?php echo $k; ?>_label]" class="control-label">
                        "<?php echo t($v); ?>" <?php echo t('Label'); ?>
                    </label>
                    <input type="text" name="fields[{{id}}][<?php echo $k; ?>_label]" id="fields[{{id}}][<?php echo $k; ?>_label]" value="{{<?php echo $k; ?>_label}}" class="form-control"/>
                </div>

                <div class="form-group">
                    <label for="fields[{{id}}][<?php echo $k; ?>_value]" class="control-label">
                        "<?php echo t($v); ?>" <?php echo t('Value'); ?>
                    </label>
                    <input type="text" name="fields[{{id}}][<?php echo $k; ?>_value]" id="fields[{{id}}][<?php echo $k; ?>_value]" value="{{<?php echo $k; ?>_value}}" class="form-control"/>
                </div>
            </div>
            <?php
        } ?>
    </div>
</div>