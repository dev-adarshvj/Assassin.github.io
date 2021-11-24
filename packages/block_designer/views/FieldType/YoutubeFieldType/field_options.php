<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="content-field-options">
    <div class="form-group-fake">
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="fields[{{id}}][size]" class="control-label"><?php echo t('Video size'); ?></label>

                    <select name="fields[{{id}}][size]" class="form-control size" id="fields[{{id}}][size]">
                        {{#select size}}
                        <option value="default">420 x 315</option>
                        <option value="medium">480 x 360</option>
                        <option value="large">640 x 480</option>
                        <option value="hd720">960 x 720</option>
                        <option value="custom"><?php echo t('Custom size'); ?></option>
                        {{/select}}
                    </select>
                </div>
            </div>

            <div class="col-sm-8 side-xs-xs">
                <div class="youtube_values hidden">
                    <div class="row">
                        <div class="col-sm-6">
                          <div class="form-group">
                              <label for="fields[{{id}}][width]" class="control-label">
                                  <?php echo t('Width'); ?>
                                  <span class="required">*</span>
                              </label>

                              <input type="text"
                                     name="fields[{{id}}][width]"
                                     id="fields[{{id}}][width]"
                                     value="{{width}}"
                                     class="form-control width"/>
                          </div>
                        </div>
                        <div class="col-sm-6 side-xs-xs">
                          <div class="form-group">
                              <label for="fields[{{id}}][height]" class="control-label">
                                  <?php echo t('Height'); ?>
                                  <span class="required">*</span>
                              </label>

                              <input type="text"
                                     name="fields[{{id}}][height]"
                                     id="fields[{{id}}][height]"
                                     value="{{height}}"
                                     class="form-control height"/>
                          </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group-fake">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="fields[{{id}}][related]" class="control-label">
                        <input type="checkbox" name="fields[{{id}}][related]" value="0" id="fields[{{id}}][related]" {{#xif " this.related == '0' " }}checked="checked"{{/xif}}>
                        <?php echo t('Hide suggested videos when the video finishes'); ?>
                    </label>
                </div>
                <div class="form-group">
                    <label for="fields[{{id}}][controls]" class="control-label">
                        <input type="checkbox" name="fields[{{id}}][controls]" value="0" id="fields[{{id}}][controls]" {{#xif " this.controls == '0' " }}checked="checked"{{/xif}}>
                        <?php echo t('Hide player controls'); ?>
                    </label>
                </div>
                <div class="form-group">
                    <label for="fields[{{id}}][showinfo]" class="control-label">
                        <input type="checkbox" name="fields[{{id}}][showinfo]" value="0" id="fields[{{id}}][showinfo]" {{#xif " this.showinfo == '0' " }}checked="checked"{{/xif}}>
                        <?php echo t('Hide video title and player actions'); ?>
                    </label>
                </div>
                <div class="form-group">
                    <label for="fields[{{id}}][privacy]" class="control-label">
                        <input type="checkbox" name="fields[{{id}}][privacy]" value="1" id="fields[{{id}}][privacy]" {{#xif " this.privacy == '1' " }}checked="checked"{{/xif}}>
                        <?php echo t('Enable privacy-enhanced mode'); ?>
                    </label>
                </div>
                <div class="form-group">
                    <label for="fields[{{id}}][loop]" class="control-label">
                        <input type="checkbox" name="fields[{{id}}][loop]" value="1" id="fields[{{id}}][loop]" {{#xif " this.loop == '1' " }}checked="checked"{{/xif}}>
                        <?php echo t('Loop'); ?>
                    </label>
                </div>
                <div class="form-group">
                    <label for="fields[{{id}}][modestbranding]" class="control-label">
                        <input type="checkbox" name="fields[{{id}}][modestbranding]" value="1" id="fields[{{id}}][modestbranding]" {{#xif " this.modestbranding == '1' " }}checked="checked"{{/xif}}>
                        <?php echo t('Hide YouTube logo'); ?>
                    </label>
                </div>
            </div>

            <div class="col-sm-6 side-xs-xs">
                <div class="form-group">
                    <label for="fields[{{id}}][autoplay]" class="control-label">
                        <input type="checkbox" name="fields[{{id}}][autoplay]" value="1" id="fields[{{id}}][autoplay]" {{#xif " this.autoplay == '1' " }}checked="checked"{{/xif}}>
                        <?php echo t('Auto play'); ?>
                    </label>
                </div>
                <div class="form-group">
                    <label for="fields[{{id}}][disablekb]" class="control-label">
                        <input type="checkbox" name="fields[{{id}}][disablekb]" value="1" id="fields[{{id}}][disablekb]" {{#xif " this.disablekb == '1' " }}checked="checked"{{/xif}}>
                        <?php echo t('Disable keyboard'); ?>
                    </label>
                </div>
                <div class="form-group">
                    <label for="fields[{{id}}][cc_load_policy]" class="control-label">
                        <input type="checkbox" name="fields[{{id}}][cc_load_policy]" value="1" id="fields[{{id}}][cc_load_policy]" {{#xif " this.cc_load_policy == '1' " }}checked="checked"{{/xif}}>
                        <?php echo t('Show closed captions by default, even if the user has turned captions off'); ?>
                    </label>
                </div>
                <div class="form-group">
                    <label for="fields[{{id}}][fs]" class="control-label">
                        <input type="checkbox" name="fields[{{id}}][fs]" value="0" id="fields[{{id}}][fs]" {{#xif " this.fs == '0' " }}checked="checked"{{/xif}}>
                        <?php echo t('Prevents the fullscreen button from displaying'); ?>
                    </label>
                </div>
                <div class="form-group">
                    <label for="fields[{{id}}][iv_load_policy]" class="control-label">
                        <input type="checkbox" name="fields[{{id}}][iv_load_policy]" value="3" id="fields[{{id}}][iv_load_policy]" {{#xif " this.iv_load_policy == '3' " }}checked="checked"{{/xif}}>
                        <?php echo t('Video annotations to be not shown by default'); ?>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group-fake">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="fields[{{id}}][start]" class="control-label">
                        <?php echo t('Start'); ?>
                        <small><?php echo t('This parameter causes the player to begin playing the video at the given number of seconds from the start of the video'); ?></small>
                    </label>

                    <input
                        type="text"
                        name="fields[{{id}}][start]"
                        id="fields[{{id}}][start]"
                        value="{{start}}"
                        data-validation-optional="true"
                        data-validation="number"
                        class="form-control"/>
                </div>
            </div>

            <div class="col-sm-6 side-xs-xs">
                <div class="form-group">
                    <label for="fields[{{id}}][end]" class="control-label">
                        <?php echo t('End'); ?>
                        <small><?php echo t('This specifies the time, measured in seconds from the start of the video, when the player should stop playing the video'); ?></small>
                    </label>

                    <input
                        type="text"
                        name="fields[{{id}}][end]"
                        id="fields[{{id}}][end]"
                        value="{{end}}"
                        data-validation-optional="true"
                        data-validation="number"
                        class="form-control"/>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="fields[{{id}}][autohide]" class="control-label">
            <?php echo t('Auto hide'); ?>
            <small><?php echo t('The video progress bar'); ?>..</small>
        </label>

        <select name="fields[{{id}}][autohide]" class="form-control" id="fields[{{id}}][autohide]">
            {{#select autohide}}
            <option value="2"><?php echo t('.. fades out while the player controls remain visible'); ?></option>
            <option value="1"><?php echo t('.. and the player controls will slide out of view a couple of seconds after the video starts playing.'); ?></option>
            <option value="0"><?php echo t('.. and the video player controls will be visible throughout the video and in fullscreen'); ?></option>
            {{/select}}
        </select>
    </div>

    <div class="form-group">
        <label for="fields[{{id}}][class]" class="control-label">
            <?php echo t('Class(es)'); ?><small><?php echo t("Class(es) to be added to the iFrame, i.e. '%s'", 'responsive-embed'); ?></small>
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
</div>