<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
{{#xif " this.base_fields == 'true' " }}
    <div class="base-fields">
        <div class="form-group-fake">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="fieldLabels[{{id}}][label]" class="control-label">
                            <?php echo t('Label'); ?>
                            <span class="required">*</span>

                            <small><?php echo t('As seen in the form'); ?></small>
                        </label>

                        <input id="fieldLabels[{{id}}][label]"
                               autocomplete="off"
                               value="{{label}}"
                               type="text" name="fields[{{id}}][label]"
                               class="form-control ccm-input-text content-field-label"
                               data-validation="required"/>
                    </div>
                </div>

                <div class="col-md-6 side-xs-xs side-sm-xs">
                      <div class="form-group">
                          <label for="fieldLabels[{{id}}][slug]" class="control-label">
                              <?php echo t('Slug'); ?>
                              <span class="required">*</span>

                              <small><?php echo t('This name will be used in the view file (a-zA-Z characters only)'); ?></small>
                          </label>

                          <input id="fieldLabels[{{id}}][slug]"
                                 autocomplete="off"
                                 value="{{slug}}"
                                 type="text" name="fields[{{id}}][slug]"
                                 class="form-control ccm-input-text" data-validation="custom"
                                 data-validation-regexp="^([a-zA-Z]+)$"/>
                      </div>
                </div>
            </div>
        </div>

        <div class="form-group-fake">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="checkbox" name="fields[{{id}}][required]" id="fieldsRequired[{{id}}]" value="1" {{#xif " this.required == '1' " }}checked="checked"{{/xif}}>
                        <label for="fieldsRequired[{{id}}]" class="control-label"><?php echo t('Required?'); ?></label>
                    </div>
                </div>

                <div class="col-md-6 side-xs-xs side-sm-xs">
                    <div class="form-group">
                        <label for="fieldLabels[{{id}}][description]" class="control-label">
                            <?php echo t('Description'); ?>
                            <small><?php echo t("Test the example"); ?>: <i class="fa fa-question-circle launch-tooltip" data-original-title="<?php echo t("The entered description will be shown with the very same question mark, next to the entered label. Users have to hover the question mark to show the description."); ?>"></i></small>
                        </label>

                        <input id="fieldLabels[{{id}}][description]"
                               autocomplete="off"
                               value="{{description}}"
                               type="text" name="fields[{{id}}][description]"
                               class="form-control ccm-input-text"/>
                    </div>
                </div>
            </div>

        </div>

        <div class="form-group-fake">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="fieldPrefixes[{{id}}]" class="control-label">
                            <?php echo t('Wrapper HTML open'); ?>
                            <small><?php echo t('i.e.'); ?> &lt;div class="abc"&gt;</small>
                         </label>

                         <textarea rows="3" name="fields[{{id}}][prefix]" id="fieldPrefixes[{{id}}]" class="form-control">{{prefix}}</textarea>
                     </div>
                </div>

                <div class="col-md-6 side-xs-xs side-sm-xs">
                    <div class="form-group">
                        <label for="fieldSuffixes[{{id}}]" class="control-label">
                            <?php echo t('Wrapper HTML close'); ?>
                            <small><?php echo t('i.e.'); ?> &lt;/div&gt;</small>
                        </label>

                        <textarea
                            rows="3"
                            name="fields[{{id}}][suffix]"
                            id="fieldSuffixes[{{id}}]"
                            class="form-control">{{suffix}}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
{{/xif}}

<?php
foreach ($field_types as $ftSlug => $ft) {
    $ftClass = $ft['class'];
    if (method_exists($ftClass, 'getFieldOptions')) {
        ?>
        {{#xif " this.type == '<?php echo $ftSlug; ?>' " }}
        <?php echo $ftClass->getFieldOptions(); ?>
        {{/xif}}<?php
    }
    if (method_exists($ftClass, 'getExtraOptions')) {
        echo $ftClass->getExtraOptions();
    }
} ?>