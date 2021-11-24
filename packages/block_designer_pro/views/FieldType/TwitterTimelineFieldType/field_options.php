<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="content-field-options">
    <div class="alert alert-info">
        <i class="fa fa-info"></i>
        <?php echo t('Include the JavaScript on your page <strong>once</strong>, ideally right after the opening %s tag.', '<code>' . htmlentities('<body>') . '</code>'); ?>
    </div>

<textarea style="margin-bottom: 10px;" class="form-control" readonly="1" rows="15" spellcheck="false" onclick="this.focus(); this.select()" tabindex="0" dir="ltr"><?php echo htmlentities("<script>
    window.twttr = (function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0], t = window.twttr || {};
        if (d.getElementById(id))return t;
        js = d.createElement(s);
        js.id = id;
        js.src = \"https://platform.twitter.com/widgets.js\";
        fjs.parentNode.insertBefore(js, fjs);
        t._e = [];
        t.ready = function (f) {
            t._e.push(f);
        };
        return t;
    }(document, \"script\", \"twitter-wjs\"));
</script>"); ?></textarea>

    <div class="form-group">
        <label for="fields[{{id}}][tweet_limit]" class="control-label">
            <?php echo t('Tweet limit'); ?>
            <br/>
            <small><?php echo t("Any value between %s and %s", 1, 20); ?></small>
        </label>
        <input type="text"
               name="fields[{{id}}][tweet_limit]"
               id="fields[{{id}}][tweet_limit]"
               value="{{tweet_limit}}"
               maxlength="2"
               data-validation-optional="true"
               data-validation="number"
               data-validation-allowing="range[1;20]"
               class="form-control"/>
    </div>

    <div class="form-group">
        <label for="fields[{{id}}][related]" class="control-label">
            <?php echo t('Web Intent Related Users'); ?>
            <br/>
            <small><?php echo t('As per the Tweet and follow buttons, you may provide a comma-separated list of user screen names as suggested followers to a user after they reply, Retweet, or favorite a Tweet in the timeline'); ?></small>
        </label>
        <input type="text"
               name="fields[{{id}}][related]"
               id="fields[{{id}}][related]"
               value="{{related}}"
               class="form-control"/>
    </div>

    <div class="form-group">
        <label for="fields[{{id}}][theme]" class="control-label">
            <?php echo t('Theme'); ?>
        </label>

        <select name="fields[{{id}}][theme]" class="form-control" id="fields[{{id}}][theme]">
            {{#select theme}}
            <option value=""><?php echo t('Light'); ?></option>
            <option value="dark"><?php echo t('Dark'); ?></option>
            {{/select}}
        </select>
    </div>

    <div class="form-group hidden">
        <label for="fields[{{id}}][source]" class="control-label">
            <?php echo t('Timeline Source'); ?>
        </label>

        <select name="fields[{{id}}][source]" class="form-control" id="fields[{{id}}][source]">
            {{#select theme}}
            <option value="user"><?php echo t('User timeline'); ?></option>
            <option value="favorites"><?php echo t('Favorites'); ?></option>
            <option value="list"><?php echo t('List'); ?></option>
            <option value="search"><?php echo t('Search'); ?></option>
            <option value="collection"><?php echo t('Collection'); ?></option>
            {{/select}}
        </select>
    </div>

    <div class="form-group">
        <label for="fields[{{id}}][politeness]" class="control-label">
            <?php echo t('ARIA politeness'); ?>
            <br/>
            <small><?php echo t('ARIA is an accessibility system that aids people using assistive technology interacting with dynamic web content'); ?></small>
        </label>

        <select name="fields[{{id}}][politeness]" class="form-control" id="fields[{{id}}][politeness]">
            {{#select politeness}}
            <option value=""><?php echo t('Polite'); ?></option>
            <option value="assertive"><?php echo t('Assertive'); ?></option>
            {{/select}}
        </select>
    </div>

    <div class="row" style="margin-top: 10px;">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="fields[{{id}}][no_header]" class="control-label">
                    <input type="checkbox" name="fields[{{id}}][no_header]" value="1" id="fields[{{id}}][no_header]"
                    {{#xif " this.no_header == '1' " }}checked="checked"{{/xif}}>
                    <?php echo t('No header'); ?>
                    <br/>
                    <small><?php echo t('Hide the timeline header'); ?></small>
                </label>
            </div>

            <div class="form-group">
                <label for="fields[{{id}}][no_footer]" class="control-label">
                    <input type="checkbox" name="fields[{{id}}][no_footer]" value="1" id="fields[{{id}}][no_footer]"
                    {{#xif " this.no_footer == '1' " }}checked="checked"{{/xif}}>
                    <?php echo t('No footer'); ?>
                    <br/>
                    <small><?php echo t('Hide the timeline footer and Tweet box, if included'); ?></small>
                </label>
            </div>

            <div class="form-group">
                <label for="fields[{{id}}][no_borders]" class="control-label">
                    <input type="checkbox" name="fields[{{id}}][no_borders]" value="1" id="fields[{{id}}][no_borders]" {{#xif " this.no_borders == '1' " }}checked="checked"{{/xif}}>
                    <?php echo t('No borders'); ?>
                    <br/>
                    <small><?php echo t('Remove all borders within the widget (between Tweets, cards, around the widget.)'); ?></small>
                </label>
            </div>

            <div class="form-group">
                <label for="fields[{{id}}][no_scrollbar]" class="control-label">
                    <input type="checkbox" name="fields[{{id}}][no_scrollbar]" value="1" id="fields[{{id}}][no_scrollbar]" {{#xif " this.no_scrollbar == '1' " }}checked="checked"{{/xif}}>
                    <?php echo t('No scrollbar'); ?>
                    <br/>
                    <small><?php echo t('Crop and hide the main timeline scrollbar, if visible. Please consider that hiding standard user interface components can affect the accessibility of your website'); ?></small>
                </label>
            </div>

            <div class="form-group">
                <label for="fields[{{id}}][transparent]" class="control-label">
                    <input type="checkbox" name="fields[{{id}}][transparent]" value="1" id="fields[{{id}}][transparent]" {{#xif " this.transparent == '1' " }}checked="checked"{{/xif}}>
                    <?php echo t('Transparent'); ?>
                    <br/>
                    <small><?php echo t('Remove the background color'); ?></small>
                </label>
            </div>
        </div>

        <div class="col-sm-6 col-xs-xs">
            <div class="form-group">
                <label for="fields[{{id}}][show_replies]" class="control-label">
                    <input type="checkbox" name="fields[{{id}}][show_replies]" value="1" id="fields[{{id}}][show_replies]" {{#xif " this.show_replies == '1' " }}checked="checked"{{/xif}}>
                    <?php echo t('Show Replies'); ?>
                </label>
            </div>
        </div>
    </div>

    <h4><?php echo t('Size'); ?>
        <small><?php echo t('Optional'); ?></small>
    </h4>

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