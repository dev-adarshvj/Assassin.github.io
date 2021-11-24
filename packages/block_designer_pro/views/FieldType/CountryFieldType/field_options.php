<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
$cl = Core::Make('lists/countries');
$countries = $cl->getCountries();
?>
<div class="content-field-options">
    <div class="form-group">
        <label for="fields[{{id}}][fallback_value]" class="control-label">
            <?php echo t('Fallback value'); ?>
        </label>

        <select name="fields[{{id}}][fallback_value]" class="form-control" id="fields[{{id}}][fallback_value]">
            {{#select fallback_value}}
            <option value="">-- <?php echo t('None'); ?> --</option><?php
            foreach ($countries as $k => $v) {
                ?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
            } ?>
            {{/select}}
        </select>
    </div>

    <div class="form-group">
        <label for="fields[{{id}}][excludes]" class="control-label">
            <?php echo t('Excluded countries'); ?>
        </label>

        <select name="fields[{{id}}][excludes][]" class="form-control" id="fields[{{id}}][excludes]" multiple="multiple" style="min-height: 200px;">
            {{#select_multiple excludes}}<?php
            foreach ($countries as $k => $v) {
                ?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
            } ?>
            {{/select_multiple}}
        </select>
    </div>

    <div class="form-group">
        <label for="fields[{{id}}][output_lang_code]" class="control-label">
            <input type="checkbox" name="fields[{{id}}][output_lang_code]" value="1" id="fields[{{id}}][output_lang_code]" {{#xif " this.output_lang_code == '1' " }}checked="checked"{{/xif}}>
            <?php echo t('Output language code instead of language name'); ?>
        </label>
    </div>
</div>