<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="content-field-options">
    <div class="alert alert-info">
        <i class="fa fa-info"></i> Include the JavaScript on your page <strong>once</strong>, ideally right after the opening <code>&lt;body&gt;</code> tag.
    </div>

    <textarea style="margin-bottom: 10px;" class="form-control" readonly="1" rows="1" spellcheck="false" onclick="this.focus(); this.select()" tabindex="0" dir="ltr"><?php echo htmlspecialchars('<script type="text/javascript" src="https://player.ooyala.com/v3/272e1da3081846939e176393081c5aef"></script>'); ?></textarea>

    <div class="form-group">
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
                <div class="ooyala_values hidden">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="fields[{{id}}][width]" class="control-label"><?php echo t('Width'); ?></label>

                                <input type="text"
                                       name="fields[{{id}}][width]"
                                       id="fields[{{id}}][width]"
                                       value="{{width}}"
                                       class="form-control width"/>
                            </div>
                        </div>
                        <div class="col-sm-6 side-xs-xs">
                            <div class="form-group">
                                <label for="fields[{{id}}][height]" class="control-label"><?php echo t('Height'); ?></label>

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

    <div class="form-group-fake row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="fields[{{id}}][tvRatingsTimer]" class="control-label">
                    <?php echo t('TV ratings timer'); ?>
                </label>

                <select name="fields[{{id}}][tvRatingsTimer]" class="form-control tvRatingsTimer" id="fields[{{id}}][tvRatingsTimer]">
                    {{#select tvRatingsTimer}}
                    <option value="always"><?php echo t('Always'); ?></option>
                    <option value="never"><?php echo t('Never'); ?></option>
                    <option value="custom"><?php echo t('Custom number of seconds'); ?></option>
                    {{/select}}
                </select>
            </div>
        </div>
        <div class="col-sm-6 side-xs-xs">
            <div class="form-group ooyala_tvratings_values hidden">
                <label for="fields[{{id}}][tvRatingsTimerSeconds]" class="control-label">
                    <?php echo t('Number of seconds'); ?>
                </label>

                <input type="text"
                       name="fields[{{id}}][tvRatingsTimerSeconds]"
                       id="fields[{{id}}][tvRatingsTimerSeconds]"
                       value="{{tvRatingsTimerSeconds}}"
                       data-validation=""
                       class="form-control tvRatingsTimerSeconds"/>
            </div>
        </div>
    </div>

    <div class="form-group-fake row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="fields[{{id}}][tvRatingsPosition]" class="control-label">
                    <?php echo t('TV ratings position'); ?>
                    <small><?php echo t('Specifies the position where the TV rating watermark will appear'); ?></small>
                </label>

                <select name="fields[{{id}}][tvRatingsPosition]" class="form-control tvRatingsPosition" id="fields[{{id}}][tvRatingsPosition]">
                    {{#select tvRatingsPosition}}
                    <option value="top-left"><?php echo t('Top left'); ?></option>
                    <option value="top-right"><?php echo t('Top right'); ?></option>
                    <option value="bottom-left"><?php echo t('Bottom left'); ?></option>
                    <option value="bottom-right"><?php echo t('Bottom right'); ?></option>
                    {{/select}}
                </select>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="fields[{{id}}][class]" class="control-label">
                    <?php echo t('Class(es)'); ?>
                    <small><?php echo t("Class(es) to be added to the element, i.e. '%s'", 'responsive-embed'); ?></small>
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
    </div>

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

    <div class="form-group">
        <label for="fields[{{id}}][loop]" class="control-label">
            <input type="checkbox" name="fields[{{id}}][loop]" value="1" id="fields[{{id}}][loop]" {{#xif " this.loop == '1' " }}checked="checked"{{/xif}}>
            <?php echo t('Loop'); ?>
        </label>
    </div>

    <div class="form-group">
        <label for="fields[{{id}}][autoplay]" class="control-label">
            <input type="checkbox" name="fields[{{id}}][autoplay]" value="1" id="fields[{{id}}][autoplay]" {{#xif " this.autoplay == '1' " }}checked="checked"{{/xif}}>
            <?php echo t('Auto play'); ?>
        </label>
    </div>

    <div class="form-group">
        <label for="fields[{{id}}][showAdMarquee]" class="control-label">
            <input type="checkbox" name="fields[{{id}}][showAdMarquee]" value="1" id="fields[{{id}}][showAdMarquee]" {{#xif " this.showAdMarquee == '1' " }}checked="checked"{{/xif}}>
            <?php echo t('Show Ad Marquee'); ?>
            <br/><small><?php echo t('Specifies whether to show or hide the ad marquee during ad playback'); ?></small>
        </label>
    </div>

    <div class="form-group">
        <label for="fields[{{id}}][enableChannels]" class="control-label">
            <input type="checkbox" name="fields[{{id}}][enableChannels]" value="1" id="fields[{{id}}][enableChannels]" {{#xif " this.enableChannels == '1' " }}checked="checked"{{/xif}}>
            <?php echo t('Enable Channels'); ?>
            <br/><small><?php echo t('Enables loading Flash videos in Channels mode. This parameter provides backwards compatibility for channels.'); ?></small>
        </label>
    </div>

    <div class="form-group">
        <label for="fields[{{id}}][prebuffering]" class="control-label">
            <input type="checkbox" name="fields[{{id}}][prebuffering]" value="1" id="fields[{{id}}][prebuffering]" {{#xif " this.prebuffering == '1' " }}checked="checked"{{/xif}}>
            <?php echo t('Prebuffering'); ?>
        </label>
    </div>

    <div class="form-group">
        <label for="fields[{{id}}][useFirstVideoFromPlaylist]" class="control-label">
            <input type="checkbox" name="fields[{{id}}][useFirstVideoFromPlaylist]" value="1" id="fields[{{id}}][useFirstVideoFromPlaylist]" {{#xif " this.useFirstVideoFromPlaylist == '1' " }}checked="checked"{{/xif}}>
            <?php echo t('Use first video from playlist'); ?>
            <br/><small><?php echo t('Check this option to set the video in the player to the first video from the first playlist.'); ?></small>
        </label>
    </div>

    <div class="form-group">
        <label for="fields[{{id}}][showInAdControlBar]" class="control-label">
            <input type="checkbox" name="fields[{{id}}][showInAdControlBar]" value="1" id="fields[{{id}}][showInAdControlBar]" {{#xif " this.showInAdControlBar == '1' " }}checked="checked"{{/xif}}>
            <?php echo t('Show in ad control bar'); ?>
            <br/><small><?php echo t('Specifies whether to show or hide the control bar during ad playback. Available for Flash and HTML5 desktop.'); ?></small>
        </label>
    </div>
</div>