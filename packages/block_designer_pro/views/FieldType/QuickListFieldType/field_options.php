<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="content-field-options">
    <div class="form-group">
        <label for="fields[{{id}}][list_type]" class="control-label"><?php echo t('List Type'); ?></label>

        <select name="fields[{{id}}][list_type]" class="form-control" id="fields[{{id}}][list_type]">
            {{#select list_type}}
            <option value="ul"><?php echo t('Unordered list (ul)'); ?></option>
            <option value="ol"><?php echo t('Ordered list (ol)'); ?></option>
            {{/select}}
        </select>
    </div>
</div>