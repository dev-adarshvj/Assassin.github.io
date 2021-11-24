<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="content-field-options">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="fields[{{id}}][sort]" class="control-label">
                    <?php echo t('Sort By'); ?>
                </label>

                <select name="fields[{{id}}][sort]" class="form-control" id="fields[{{id}}][sort]">
                    {{#select sort}}
                    <option value="">-- <?php echo t('None'); ?> --</option>
                    <option value="uID"><?php echo t('User ID'); ?></option>
                    <option value="uName"><?php echo t('User Name'); ?></option>
                    {{/select}}
                </select>
            </div>

            <div class="form-group">
                <label for="fields[{{id}}][unvalidated]" class="control-label">
                    <input type="checkbox" name="fields[{{id}}][unvalidated]" value="1" id="fields[{{id}}][unvalidated]" {{#xif " this.unvalidated == '1' "}}checked="checked"{{/xif}}>
                    <?php echo t('Include Unvalidated Users'); ?>
                </label>
            </div>

            <div class="form-group">
                <label for="fields[{{id}}][inactive]" class="control-label">
                    <input type="checkbox" name="fields[{{id}}][inactive]" value="1" id="fields[{{id}}][inactive]" {{#xif " this.inactive == '1' "}}checked="checked"{{/xif}}>
                    <?php echo t('Include Inactive Users'); ?>
                </label>
            </div>
        </div>

        <div class="col-sm-6 col-xs-xs">
            <div class="form-group">
                <label for="fields[{{id}}][output]" class="control-label">
                    <?php echo t('Output'); ?>
                    <br/>
                    <small><?php echo t('Which attribute of the chosen user needs to be outputted in the view template'); ?></small>
                </label>

                <select name="fields[{{id}}][output]" class="form-control" id="fields[{{id}}][output]">
                    {{#select output}}
                    <option value="uID"><?php echo t('User ID'); ?></option>
                    <option value="uName"><?php echo t('User Name'); ?></option>
                    <option value="uEmail"><?php echo t('User Email'); ?></option>
                    {{/select}}
                </select>
            </div>

            <div class="form-group">
                <label for="fields[{{id}}][link]" class="control-label">
                    <input class="make_link" type="checkbox" name="fields[{{id}}][link]" value="1" id="fields[{{id}}][link]" {{#xif " this.link == '1' " }}checked="checked"{{/xif}}>
                    <?php echo t('Wrap anchor to member page around output'); ?>
                    <br/>
                    <small><?php echo t("Make sure <a href='%s' target='_blank'>Public Profiles</a> are enabled", URL::to('/dashboard/system/registration/profiles')); ?></small>
                </label>
            </div>

            <div class="link-values form-group-fake hidden">
                <div class="form-group">
                    <label for="fields[{{id}}][link_class]" class="control-label">
                        <?php echo t('Class(es)'); ?>
                        <small><?php echo t("Class(es) to be added to your link, i.e. '%s'", 'user-anchor'); ?></small>
                    </label>
                    <input type="text"
                           name="fields[{{id}}][link_class]"
                           id="fields[{{id}}][link_class]"
                           value="{{link_class}}"
                           data-validation-optional="true"
                           data-validation="custom"
                           data-validation-length="min3"
                           data-validation-regexp="^([a-zA-Z]+)([a-zA-Z-0-9 ]+)$"
                           class="form-control"/>
                </div>
            </div>
        </div>
    </div>
</div>