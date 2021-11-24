<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="content-field-options">
    <div class="form-group">
        <label for="fields[{{id}}][nl2br]" class="control-label">
            <input type="checkbox" name="fields[{{id}}][nl2br]" value="1" id="fields[{{id}}][nl2br]" {{#xif " this.nl2br == '1' " }}checked="checked"{{/xif}}>
            <?php echo t('New line to blank rule'); ?>
            <br/><small>
                <?php echo t("Returns entered string with '%s' or '%s' inserted before all newlines", '&lt;br /&gt;', '&lt;br&gt;'); ?>
            </small>
        </label>
    </div>

    <div class="form-group">
        <label for="fields[{{id}}][skip_h]" class="control-label">
            <input type="checkbox" name="fields[{{id}}][skip_h]" value="1" id="fields[{{id}}][skip_h]" {{#xif " this.skip_h == '1' " }}checked="checked"{{/xif}}>
            <?php echo t("Skip converting special characters to HTML entities (concrete5's 'h' function)"); ?>
            <br/><small>
                <?php echo t("This makes it possible to enter HTML characters in your text area"); ?>
            </small>
        </label>
    </div>
</div>