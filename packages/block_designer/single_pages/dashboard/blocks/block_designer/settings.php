<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="block-designer-container">
	<form action="<?php echo $this->action(''); ?>" method="post">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<?php echo $form->label('ft_hide', t("Hide Field Types")); ?>

					<div style="width: 100%;">
						<?php echo $form->selectMultiple('ft_hide', $ftHideOptions, $ft_hide, ['class' => 'ft_hide']); ?>
					</div>

					<p>
						<small><?php echo t("This will only hide them from the 'Add a field' list, you will still be able to load configurations with the hidden field types."); ?></small>
					</p>
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
					<?php echo $form->label('ft_sort', t("Field Type Sorting")); ?>
					<?php echo $form->select('ft_sort', $ftSortOptions, $ft_sort); ?>
				</div>

				<div class="form-group form-group-custom-order" style="display: none;">
					<?php echo $form->label('ft_order', t("Field Type Custom Order")); ?>

					<div class="alert alert-warning">
						<p>
							<i class="fa fa-info-circle"></i>
							<?php echo t("Drag and drop each field type in the position you want them in. The ones that are colored red, are fields that are selected under the 'Hide Field Types'. This way you know which ones are hidden and you could drag them to the bottom of the list."); ?>
						</p>
					</div>

					<ul class="item-select-list block-types-order">
						<?php
						foreach ($ftOrderOptions as $k => $v) {
							?>
							<li>
								<a href="#" class="btn btn-primary" data-attr-handle="<?php echo $k; ?>">
									<span class="hidden"><?php echo $form->checkbox('ft_order[]', $k, true); ?></span>
									<img src="<?php echo $v['icon']; ?>" alt="<?php echo $v['name']; ?> <?php echo t("Icon"); ?>"/>
									<?php echo $v['name']; ?>
								</a>
							</li>
							<?php
						} ?>
					</ul>
				</div>
			</div>
		</div>

		<div class="ccm-dashboard-form-actions-wrapper">
			<div class="ccm-dashboard-form-actions">
				<?php echo $this->controller->token->output('block_designer_settings'); ?>

				<button type="submit" name="submit" class="btn btn-primary">
					<?php echo t("Save"); ?>
				</button>
			</div>
		</div>
	</form>
</div>