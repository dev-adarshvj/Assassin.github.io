<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="content-field-options">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="fields[{{id}}][class]" class="control-label">
                    <?php echo t('Class(es)'); ?>
                    <small><?php echo t("Class(es) to be added to your image, i.e. '%s'", 'img-responsive img-rounded'); ?></small>
                </label>
                <input type="text"
                       name="fields[{{id}}][class]"
                       id="fields[{{id}}][class]"
                       value="{{class}}"
                       data-validation-optional="true"
                       data-validation="custom"
                       data-validation-length="min3"
                       data-validation-regexp="^([a-zA-Z]+)([a-zA-Z-_ ]+)$"
                       class="form-control"/>
            </div>

            <div class="form-group">
                <label for="fields[{{id}}][link]" class="control-label">
                    <?php echo t('Link'); ?>
                </label>

                <select name="fields[{{id}}][link]" class="form-control make_link" id="fields[{{id}}][link]">
                    {{#select link}}
                    <option value="">-- <?php echo t('None'); ?> --</option>
                    <option value="1"><?php echo t('Page'); ?></option>
                    <option value="2"><?php echo t('URL'); ?></option>
                    {{/select}}
                </select>
            </div>

            <div class="form-group-fake">
                <div class="link-values" style="display: none;">
                    <div class="form-group">
                        <label for="fields[{{id}}][link_class]" class="control-label">
                            <?php echo t('Class(es)'); ?>
                            <small><?php echo t("Class(es) to be added to your link, i.e. '%s'", 'product-anchor'); ?></small>
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

                    <div class="form-group">
                        <input type="checkbox" name="fields[{{id}}][url_target]" value="1" id="fields[{{id}}][url_target]" {{#xif " this.url_target == '1' " }}checked="checked"{{/xif}}>
                        <label for="fields[{{id}}][url_target]" class="control-label"><?php echo t('Link opens in a new window'); ?></label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="fields[{{id}}][responsive_image]" class="control-label">
                    <input type="checkbox" name="fields[{{id}}][responsive_image]" value="1" id="fields[{{id}}][responsive_image]" {{#xif " this.responsive_image == '1' " }}checked="checked"{{/xif}}>
                    <?php echo t('Responsive image'); ?>
                </label>
                <br/><small>
                    <?php echo t('Uses Concrete5 image helper, to output the image.'); ?>
                </small>
            </div>
            <div class="form-group">
                <label for="fields[{{id}}][output_src_only]" class="control-label">
                    <input type="checkbox" name="fields[{{id}}][output_src_only]" value="1" id="fields[{{id}}][output_src_only]" {{#xif " this.output_src_only == '1' " }}checked="checked"{{/xif}}>
                    <?php echo t('Only output the source (path) of the image'); ?>
                </label>
            </div>

            <div class="form-group">
                <label for="fields[{{id}}][thumbnail]" class="control-label">
                    <input class="make_thumbnail" type="checkbox" name="fields[{{id}}][thumbnail]" value="1" id="fields[{{id}}][thumbnail]" {{#xif " this.thumbnail == '1' " }}checked="checked"{{/xif}}>
                    <?php echo t('Make a thumbnail of the image'); ?>
                </label>
            </div>

            <div class="thumbnail-values" style="display: none;">
                <div class="alert alert-info">
                    <i class="fa fa-info"></i> <?php echo t('Width and height are required upon making the image a thumbnail. Both values are being entered in whole pixels (no decimals allowed).'); ?>
                </div>

                <div class="form-group">
                    <label for="fields[{{id}}][crop]" class="control-label">
                        <input type="checkbox" name="fields[{{id}}][crop]" value="1" id="fields[{{id}}][crop]" {{#xif " this.crop == '1' " }}checked="checked"{{/xif}}>
                        <?php echo t('Crop the image to the given width and height'); ?>
                    </label>
                </div>

                <div class="form-group">
                    <label for="fields[{{id}}][width]" class="control-label">
                        <?php echo t('Width'); ?>
                    </label>
                    <input type="text"
                           name="fields[{{id}}][width]"
                           id="fields[{{id}}][width]"
                           value="{{width}}"
                           data-validation-optional="true"
                           data-validation="number"
                           data-validation-allowing="range[1;99999999999]"
                           class="form-control width"/>
                </div>

                <div class="form-group">
                    <label for="fields[{{id}}][height]" class="control-label">
                        <?php echo t('Height'); ?>
                    </label>
                    <input type="text"
                           name="fields[{{id}}][height]"
                           id="fields[{{id}}][height]"
                           value="{{height}}"
                           data-validation-optional="true"
                           data-validation="number"
                           data-validation-allowing="range[1;99999999999]"
                           class="form-control height"/>
                </div>
            </div>

            <div class="form-group" style="margin-top: 10px;">
                <label for="fields[{{id}}][thumbnail_handle]" class="control-label">
                    <?php echo t('Thumbnail handle'); ?>
                    <small><?php echo t("If the handle is found, it will use its data to create the image with the given width/height (go to %s).", sprintf('<a href="%s" target="_blank">%s</a>', URL::to('/dashboard/system/files/thumbnails'), t("Thumbnails"))); ?></small>
                </label>

                <input type="text"
                       name="fields[{{id}}][thumbnail_handle]"
                       id="fields[{{id}}][thumbnail_handle]"
                       value="{{thumbnail_handle}}"
                       data-validation-optional="true"
                       data-validation="custom"
                       data-validation-length="min3"
                       data-validation-regexp="^([a-zA-Z]+)([a-zA-Z-_]+)$"
                       class="form-control thumbnail_handle"/>
            </div>
        </div>
    </div>
</div>