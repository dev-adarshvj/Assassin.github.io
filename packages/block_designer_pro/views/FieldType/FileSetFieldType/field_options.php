<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="content-field-options">
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label for="fields[{{id}}][title_displayed]" class="control-label">
					<input type="checkbox" name="fields[{{id}}][title_displayed]" class="title_display" value="1" id="fields[{{id}}][title_displayed]" {{#xif " this.title_displayed == '1' " }}checked="checked"{{/xif}}>
					<?php echo t("Display file set title"); ?>
				</label>
			</div>

			<div class="form-group-fake">
				<div class="title-values" style="display: none;">
					<div class="form-group">
						<label for="fields[{{id}}][title_class]" class="control-label">
							<?php echo t('Class(es)'); ?>
							<small><?php echo t("Class(es) to be added to your title, i.e. '%s'", 'page-title'); ?></small>
						</label>
						<input type="text"
						       name="fields[{{id}}][title_class]"
						       id="fields[{{id}}][title_class]"
						       value="{{title_class}}"
						       data-validation-optional="true"
						       data-validation="custom"
						       data-validation-length="min3"
						       data-validation-regexp="^([a-zA-Z]+)([a-zA-Z-_ ]+)$"
						       class="form-control"/>
					</div>

					<div class="form-group">
						<label for="fields[{{id}}][title_wrapper]" class="control-label">
							<?php echo t('Title wrapper'); ?>
						</label>

						<select name="fields[{{id}}][title_wrapper]" class="form-control" id="fields[{{id}}][title_wrapper]">
							{{#select title_wrapper}}
							<?php
							foreach ($titleWrapperOptions as $k => $v) {
								?>
								<option value="<?php echo $k; ?>"><?php echo $v; ?></option>
								<?php
							} ?>
							{{/select}}
						</select>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label for="fields[{{id}}][list_displayed]" class="control-label">
					<input type="checkbox" name="fields[{{id}}][list_displayed]" class="list_display" value="1" id="fields[{{id}}][list_displayed]" {{#xif " this.list_displayed == '1' " }}checked="checked"{{/xif}}>
					<?php echo t("Display file set list of files"); ?>
				</label>
			</div>

			<div class="form-group-fake">
				<div class="list-values" style="display: none;">
					<div class="form-group">
						<label for="fields[{{id}}][list_class]" class="control-label">
							<?php echo t('Class(es)'); ?>
							<small><?php echo t("Class(es) to be added to your list, i.e. '%s'", 'list-unstyled'); ?></small>
						</label>
						<input type="text"
						       name="fields[{{id}}][list_class]"
						       id="fields[{{id}}][list_class]"
						       value="{{list_class}}"
						       data-validation-optional="true"
						       data-validation="custom"
						       data-validation-length="min3"
						       data-validation-regexp="^([a-zA-Z]+)([a-zA-Z-_ ]+)$"
						       class="form-control"/>
					</div>

					<div class="form-group-fake">
						<div class="alert alert-info">
							<?php echo t("A simple unordered list will be outputted, where the file name is displayed and clickable (opens in a new window)."); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
