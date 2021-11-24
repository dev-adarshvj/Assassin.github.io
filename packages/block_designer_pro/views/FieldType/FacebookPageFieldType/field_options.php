<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="content-field-options">
    <div class="alert alert-info">
        <i class="fa fa-info"></i> <?php echo t('Include the JavaScript on your page <strong>once</strong>, ideally right after the opening %s tag.', '<code>' . htmlentities('<body>') . '</code>'); ?>
    </div>

<textarea style="margin-bottom: 10px;" class="form-control" readonly="1" rows="9" spellcheck="false" onclick="this.focus(); this.select()" tabindex="0" dir="ltr">&lt;div id="fb-root"&gt;&lt;/div&gt;
&lt;script&gt;(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&amp;version=v2.3";
    fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
&lt;/script&gt;</textarea>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="fields[{{id}}][width]" class="control-label">
                    <?php echo t('Width'); ?>
                    <small><?php echo t('The pixel width of the plugin. Min. is 280 & Max. is 500'); ?></small>
                </label>
                <input type="text"
                       name="fields[{{id}}][width]"
                       id="fields[{{id}}][width]"
                       size="3"
                       value="{{width}}"
                       maxlength="5"
                       data-validation-optional="true"
                       data-validation="number"
                       data-validation-allowing="range[280;500]"
                       class="form-control"/>
            </div>

            <div class="form-group">
                <label for="fields[{{id}}][height]" class="control-label">
                    <?php echo t('Height'); ?>
                    <small><?php echo t('The maximum pixel height of the plugin. Min. is 130'); ?></small>
                </label>
                <input type="text"
                       name="fields[{{id}}][height]"
                       id="fields[{{id}}][height]"
                       size="3"
                       value="{{height}}"
                       maxlength="5"
                       data-validation-optional="true"
                       data-validation="number"
                       data-validation-allowing="range[130;9999]"
                       class="form-control"/>
            </div>
        </div>

        <div class="col-sm-6 col-xs-xs">
            <div class="form-group">
                <label for="fields[{{id}}][hide_cover]" class="control-label">
                    <input type="checkbox" name="fields[{{id}}][hide_cover]" value="1" id="fields[{{id}}][hide_cover]" {{#xif " this.hide_cover == '1' " }}checked="checked"{{/xif}}>
                    <?php echo t('Hide cover photo in the header'); ?>
                </label>
            </div>

            <div class="form-group">
                <label for="fields[{{id}}][show_facepile]" class="control-label">
                    <input type="checkbox" name="fields[{{id}}][show_facepile]" value="1" id="fields[{{id}}][show_facepile]" {{#xif " this.show_facepile == '1' "}}checked="checked"{{/xif}}>
                    <?php echo t('Show profile photos when friends like this'); ?>
                </label>
            </div>

            <div class="form-group">
                <label for="fields[{{id}}][show_posts]" class="control-label">
                    <input type="checkbox" name="fields[{{id}}][show_posts]" value="1" id="fields[{{id}}][show_posts]" {{#xif " this.show_posts == '1' "}}checked="checked"{{/xif}}>
                    <?php echo t("Show posts from the Page's timeline."); ?>
                </label>
            </div>
        </div>
    </div>
</div>