<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
{{#xif " this.can_repeat == 'true' " }}
<div class="form-group form-group-can_repeat">
    <label for="fields[{{id}}][repeatable]" class="control-label">
        <?php echo t('Repeatable for'); ?>
    </label>

    <select name="fields[{{id}}][repeatable]" id="fields[{{id}}][repeatable]" class="form-control select-repeatable" data-value="{{repeatable}}">
        <option value="">-- <?php echo t('None'); ?> --</option>
    </select>
</div>
{{/xif}}