<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="content-field-options">
    <div class="form-group">
        <label for="fields[{{id}}][autoplay]" class="control-label">
            <input type="checkbox" name="fields[{{id}}][autoplay]" value="1" id="fields[{{id}}][autoplay]" {{#xif " this.autoplay == '1' "}}checked="checked"{{/xif}}>
            <?php echo t('Autoplay this video.'); ?>
            <br/><small><?php echo t('Play the video automatically on load'); ?></small>
        </label>
    </div>

    <div class="form-group">
        <label for="fields[{{id}}][class]" class="control-label">
            <?php echo t('Class(es)'); ?>
            <small><?php echo t("Class(es) to be added to the iFrame, i.e. '%s'", 'responsive-embed'); ?></small>
        </label>
        <input type="text"
               name="fields[{{id}}][class]"
               id="fields[{{id}}][class]"
               value="{{class}}"
               data-validation-optional="true"
               data-validation="custom"
               data-validation-length="min3"
               data-validation-regexp="^([a-zA-Z]+)([a-zA-Z-0-9_ ]+)$"
               class="form-control"/>
    </div>

    <h4><?php echo t('Size'); ?></h4>

    <div class="form-group-fake">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="fields[{{id}}][width]" class="control-label">
                        <?php echo t('Width'); ?>
                    </label>
                    <input type="text"
                           name="fields[{{id}}][width]"
                           id="fields[{{id}}][width]"
                           value="{{width}}"
                           maxlength="5"
                           class="form-control"/>
                </div>
            </div>

            <div class="col-sm-6 col-xs-xs">
                <div class="form-group">
                    <label for="fields[{{id}}][height]" class="control-label">
                        <?php echo t('Height'); ?>
                    </label>
                    <input type="text"
                           name="fields[{{id}}][height]"
                           id="fields[{{id}}][height]"
                           value="{{height}}"
                           maxlength="5"
                           class="form-control"/>
                </div>
            </div>
        </div>
    </div>
</div>