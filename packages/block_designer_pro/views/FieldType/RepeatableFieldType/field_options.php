<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="content-field-options">
    <div class="form-group-fake">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="fields[{{id}}][child_prefix]" class="control-label">
                        <?php echo t('Child Wrapper HTML open'); ?>
                        <small><?php echo t('i.e.'); ?> &lt;div class="abc"&gt;</small>
                    </label>

                    <textarea rows="3" name="fields[{{id}}][child_prefix]" id="fields[{{id}}][child_prefix]" class="form-control">{{child_prefix}}</textarea>
                </div>
            </div>

            <div class="col-md-6 side-xs-xs side-sm-xs">
                <div class="form-group">
                    <label for="fields[{{id}}][child_suffix]" class="control-label">
                        <?php echo t('Child Wrapper HTML close'); ?>
                        <small><?php echo t('i.e.'); ?> &lt;/div&gt;</small>
                    </label>

                    <textarea rows="3" name="fields[{{id}}][child_suffix]" id="fields[{{id}}][child_suffix]" class="form-control">{{child_suffix}}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group-fake">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="fields[{{id}}][no_collapse]" class="control-label">
                        <input type="checkbox" name="fields[{{id}}][no_collapse]" value="1" id="fields[{{id}}][no_collapse]" {{#xif " this.no_collapse == '1' " }}checked="checked"{{/xif}}>
                        <?php echo t('Do not show the collapse/expand button'); ?>
                        <br/>
                        <small><?php echo t('Being able to collapse will make it easier to rearrange items'); ?></small>
                    </label>
                </div>

                <div class="form-group">
                    <label for="fields[{{id}}][shuffle]" class="control-label">
                        <input type="checkbox" name="fields[{{id}}][shuffle]" value="1" id="fields[{{id}}][shuffle]" {{#xif " this.shuffle == '1' " }}checked="checked"{{/xif}}>
                        <?php echo t('Shuffle items'); ?>
                        <br/>
                        <small><?php echo t('Shuffling items will make it possible to show items in a randomized order, instead of displaying using the set position.'); ?></small>
                    </label>
                </div>

                <div class="form-group">
                    <label for="fields[{{id}}][prepend]" class="control-label">
                        <input type="checkbox" name="fields[{{id}}][prepend]" value="1" id="fields[{{id}}][prepend]" {{#xif " this.prepend == '1' " }}checked="checked"{{/xif}}>
                        <?php echo t('Add new entry at the top'); ?>
                        <br/>
                        <small><?php echo t("Clicking '%s' will add an item at the bottom by default. You can chose to add new items at the top instead.", t("Add Entry")); ?></small>
                    </label>
                </div>

                <div class="form-group">
                    <label for="fields[{{id}}][title_me]" class="control-label">
                        <?php echo t('Field to use as title'); ?>
                        <small><?php echo t("Select a '%s' field that will be used as title for each repeatable item (when not empty)", t("Text Box")); ?></small>
                    </label>

                    <select name="fields[{{id}}][title_me]" class="form-control title_me" id="fields[{{id}}][title_me]" data-value="{{title_me}}">
                        <option value="">-- <?php echo t('None'); ?> --</option>
                    </select>
                </div>
            </div>

            <div class="col-md-6 side-xs-xs side-sm-xs">
                <div class="form-group">
                    <label for="fields[{{id}}][min_length]" class="control-label">
                        <?php echo t('Minimum number of items'); ?>
                        <small><?php echo t('Between %s and %s', 0, 999); ?></small>
                    </label>

                    <input type="text"
                           name="fields[{{id}}][min_length]"
                           id="fields[{{id}}][min_length]"
                           value="{{min_length}}"
                           maxlength="3"
                           data-validation-optional="true"
                           data-validation="number"
                           data-validation-allowing="range[0;999]"
                           class="form-control ccm-input-text"/>
                </div>

                <div class="form-group">
                    <label for="fields[{{id}}][max_length]" class="control-label">
                        <?php echo t('Maximum number of items'); ?>
                        <small><?php echo t('Between %s and %s', 1, 999); ?></small>
                    </label>

                    <input type="text"
                           name="fields[{{id}}][max_length]"
                           id="fields[{{id}}][max_length]"
                           value="{{max_length}}"
                           maxlength="3"
                           data-validation-optional="true"
                           data-validation="number"
                           data-validation-allowing="range[1;999]"
                           class="form-control ccm-input-text"/>
                </div>

                <div class="form-group">
                    <label for="fields[{{id}}][add_entry_text]" class="control-label">
                        <?php echo t('Add Entry text'); ?>
                        <small><?php echo t("Text to display on the '%s' button", t('Add Entry')); ?></small>
                    </label>

                    <input type="text"
                           name="fields[{{id}}][add_entry_text]"
                           id="fields[{{id}}][add_entry_text]"
                           value="{{add_entry_text}}"
                           placeholder="<?php echo t('Add Entry'); ?>"
                           class="form-control ccm-input-text"/>
                </div>
            </div>
        </div>
    </div>
</div>