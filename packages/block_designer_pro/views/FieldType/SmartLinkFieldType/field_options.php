<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="content-field-options">
	<div class="form-group-fake row">
		<div class="col-sm-6">
			<div data-smart-link-type="file">
				<h4><?php echo t("File"); ?></h4>

				<div class="form-group">
					<input type="checkbox" name="fields[{{id}}][file_target_blank]" value="1" id="fields[{{id}}][file_target_blank]" {{#xif " this.file_target_blank == '1' " }}checked="checked"{{/xif}}>
					<label for="fields[{{id}}][file_target_blank]" class="control-label"><?php echo t('%s opens in a new window', t("File")); ?></label>
				</div>

				<div class="form-group">
					<label for="fields[{{id}}][file_download]" class="control-label">
						<input type="checkbox" name="fields[{{id}}][file_download]" value="1" id="fields[{{id}}][file_download]" {{#xif " this.file_download == '1' " }}checked="checked"{{/xif}}>
						<?php echo t('Use download link, instead of relative path link (if available)'); ?>
					</label>
				</div>
			</div>

			<div data-smart-link-type="image">
				<h4><?php echo t("Image"); ?></h4>

				<div class="form-group">
					<input type="checkbox" name="fields[{{id}}][image_target_blank]" value="1" id="fields[{{id}}][image_target_blank]" {{#xif " this.image_target_blank == '1' " }}checked="checked"{{/xif}}>
					<label for="fields[{{id}}][image_target_blank]" class="control-label"><?php echo t('%s opens in a new window', t("Image")); ?></label>
				</div>
			</div>

			<div data-smart-link-type="url">
				<h4><?php echo t("URL"); ?></h4>

				<div class="form-group">
					<input type="checkbox" name="fields[{{id}}][url_target_blank]" value="1" id="fields[{{id}}][url_target_blank]" {{#xif " this.url_target_blank == '1' " }}checked="checked"{{/xif}}>
					<label for="fields[{{id}}][url_target_blank]" class="control-label"><?php echo t('%s opens in a new window', t("URL")); ?></label>
				</div>
			</div>

			<div data-smart-link-type="relative_url">
				<h4><?php echo t("Relative URL"); ?></h4>

				<div class="form-group">
					<input type="checkbox" name="fields[{{id}}][relative_url_target_blank]" value="1" id="fields[{{id}}][relative_url_target_blank]" {{#xif " this.relative_url_target_blank == '1' " }}checked="checked"{{/xif}}>
					<label for="fields[{{id}}][relative_url_target_blank]" class="control-label"><?php echo t('%s opens in a new window', t("Relative URL")); ?></label>
				</div>
			</div>
		</div>

		<div class="col-sm-6">
			<h4><?php echo t("Exclude link options"); ?></h4>

			<div class="form-group">
				<label for="fields[{{id}}][page]" class="control-label">
					<input type="checkbox" name="fields[{{id}}][page]" value="1" id="fields[{{id}}][page]" {{#xif " this.page == '1' " }}checked="checked"{{/xif}}>
					<?php echo t('Page'); ?>
				</label>
			</div>

			<div class="form-group">
				<label for="fields[{{id}}][file]" class="control-label">
					<input type="checkbox" name="fields[{{id}}][file]" value="1" id="fields[{{id}}][file]" class="exclude_file" {{#xif " this.file == '1' " }}checked="checked"{{/xif}}>
					<?php echo t('File'); ?>
				</label>
			</div>

			<div class="form-group">
				<label for="fields[{{id}}][image]" class="control-label">
					<input type="checkbox" name="fields[{{id}}][image]" value="1" id="fields[{{id}}][image]" class="exclude_image" {{#xif " this.image == '1' " }}checked="checked"{{/xif}}>
					<?php echo t('Image'); ?>
				</label>
			</div>

			<div class="form-group">
				<label for="fields[{{id}}][url]" class="control-label">
					<input type="checkbox" name="fields[{{id}}][url]" value="1" id="fields[{{id}}][url]" class="exclude_url" {{#xif " this.url == '1' " }}checked="checked"{{/xif}}>
					<?php echo t('URL'); ?>
				</label>
			</div>

			<div class="form-group">
				<label for="fields[{{id}}][relative_url]" class="control-label">
					<input type="checkbox" name="fields[{{id}}][relative_url]" value="1" id="fields[{{id}}][relative_url]" class="exclude_relative_url" {{#xif " this.relative_url == '1' " }}checked="checked"{{/xif}}>
					<?php echo t('Relative URL'); ?>
				</label>
			</div>
		</div>
	</div>

    <div class="form-group">
        <label for="fields[{{id}}][class]" class="control-label">
            <?php echo t('Class(es)'); ?>
            <small><?php echo t("Class(es) to be added to your link, i.e. '%s'", 'product-anchor'); ?></small>
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