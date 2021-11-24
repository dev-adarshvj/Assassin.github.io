<?php namespace RamonLeenders\BlockDesigner\FieldType\WysiwygFieldType;

defined('C5_EXECUTE') or die("Access Denied.");

use RamonLeenders\BlockDesigner\FieldType\FieldType;

class WysiwygFieldType extends FieldType
{
	protected $ftHandle = 'wysiwyg';
	protected $dbType = 'X2';
	protected $uses = ['Concrete\Core\Editor\LinkAbstractor'];
	protected $canRepeat = true;

	public function getFieldName()
	{
		return t("WYSIWYG");
	}

	public function getFieldDescription()
	{
		return t("A 'What-You-See-Is-What-You-Get' text area");
	}

	public function getSearchableContent()
	{
		if ($this->getRepeating()) {
			$slug = '$' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"]';
			return 'if (isset(' . $slug . ') && trim(' . $slug . ') != "") {
                $content[] = ' . $slug . ';
            }';
		} else {
			return '$content[] = $this->' . $this->data['slug'] . ';';
		}
	}

	public function getViewFunctionContents()
	{
		if ($this->getRepeating()) {
			return '$' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"] = isset($' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"]) ? LinkAbstractor::translateFrom($' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"]) : null;';
		} else {
			return '$this->set(\'' . $this->data['slug'] . '\', LinkAbstractor::translateFrom($this->' . $this->data['slug'] . '));';
		}
	}

	public function getSaveFunctionContents()
	{
		if ($this->getRepeating()) {
			return '$data[\'' . $this->data['slug'] . '\'] = isset($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\']) ? LinkAbstractor::translateTo($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\']) : null;';
		} else {
			return '$args[\'' . $this->data['slug'] . '\'] = LinkAbstractor::translateTo($args[\'' . $this->data['slug'] . '\']);';
		}
	}

	public function getValidateFunctionContents()
	{
		if ($this->getRepeating()) {
			return 'if (in_array("' . $this->data['slug'] . '", $this->btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']) && (!isset($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']) || trim($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']) == "")) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("' . h($this->data['label']) . '"), t("' . h($this->data['parent']['label']) . '"), $' . $this->data['parent']['slug'] . '_k));
                        }';
		} else {
			return 'if (in_array("' . $this->data['slug'] . '", $this->btFieldsRequired) && (trim($args["' . $this->data['slug'] . '"]) == "")) {
            $e->add(t("The %s field is required.", t("' . h($this->data['label']) . '")));
        }';
		}
	}

	public function getEditFunctionContents()
	{
		$return = null;
		if ($this->getRepeating()) {
			$slug = '$' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\']';
			$return .= PHP_EOL . '        foreach ($' . $this->data['parent']['slug'] . '_items as &$' . $this->data['parent']['slug'] . '_item) {
            ' . $slug . ' = isset(' . $slug . ') ? LinkAbstractor::translateFromEditMode(' . $slug . ') : null;
        }';
		} else {
			$return .= '
        $this->set(\'' . $this->data['slug'] . '\', LinkAbstractor::translateFromEditMode($this->' . $this->data['slug'] . '));';
		}
		return $return;
	}

	protected function getRepeatableUpdateItemJSClassName()
	{
		return sprintf('ft-%s-%s', $this->data['parent']['slug'], $this->data['slug']);
	}

	public function getRepeatableUpdateItemJS()
	{
		$class = $this->getRepeatableUpdateItemJSClassName();
		return 'blockDesignerEditor(\'#\' + ($(newField).find(\'textarea.' . $class . '\').attr(\'id\')).replace(/(:|\.|\[|\]|,|=|@)/g, "\\\\$1"));';
	}

	public function getRepeatableDeleteItemJS()
	{
		$class = $this->getRepeatableUpdateItemJSClassName();
		return 'if(typeof CKEDITOR !== \'undefined\'){
                    var blockDesignerEditorInstance = CKEDITOR.instances[$(sortableItem).find(\'textarea.' . $class . '\').attr(\'id\')];
                    if (blockDesignerEditorInstance) {
                        blockDesignerEditorInstance.destroy();
                    }
                }';
	}

	public function getViewContents()
	{
		$slug = $this->getRepeating() ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '"]' : $this->data['slug'];
		return '<?php if (isset($' . $slug . ') && trim($' . $slug . ') != "") { ?>' . $this->data['prefix'] . '<?php echo $' . $slug . '; ?>' . $this->data['suffix'] . '<?php } ?>';
	}

	public function getFormContents()
	{
		$repeating = $this->getRepeating();
		$btFieldsRequired = $repeating ? '$btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']' : '$btFieldsRequired';
		$return = '<div class="form-group">
    ' . parent::generateFormContent('label', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => $this->data['label'], 'description' => $this->data['description']], $repeating) . '
    ' . parent::generateFormContent('required', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'array' => $btFieldsRequired], $repeating) . '
    ' . parent::generateFormContent('editor', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'attributes' => ['class' => $this->getRepeatableUpdateItemJSClassName()]], $repeating) . '
</div>';
		return $return;
	}

	public function getFormContentsPre()
	{
		if ($this->getRepeating() && $this->data['ft_count_repeatable'] <= 0) {
			return '<?php
	$core_editor = Core::make(\'editor\');
	if (method_exists($core_editor, \'getEditorInitJSFunction\')) {
		/* @var $core_editor \Concrete\Core\Editor\CkeditorEditor */
		?>
		<script type="text/javascript">var blockDesignerEditor = <?php echo $core_editor->getEditorInitJSFunction(); ?>;</script>
	<?php
	} else {
	/* @var $core_editor \Concrete\Core\Editor\RedactorEditor */
	if(method_exists($core_editor, \'requireEditorAssets\')){
		$core_editor->requireEditorAssets();
	} ?>
		<script type="text/javascript">
			var blockDesignerEditor = function (identifier) {$(identifier).redactor(<?php echo json_encode(array(\'plugins\' => [\'concrete5magic\'] + $core_editor->getPluginManager()->getSelectedPlugins(), \'minHeight\' => 300,\'concrete5\' => array(\'filemanager\' => $core_editor->allowFileManager(), \'sitemap\' => $core_editor->allowSitemap()))); ?>).on(\'remove\', function () {$(this).redactor(\'core.destroy\');});};
		</script>
		<?php
	} ?>';
		}
		return null;
	}

	public function getDbFields()
	{
		return [
			[
				'name' => $this->data['slug'],
				'type' => $this->getDbType(),
			]
		];
	}

	public function getAssets()
	{
		return [
			'addEdit' => [
				'require' => [
					[
						'handle' => 'redactor',
					],
					[
						'handle' => 'core/file-manager',
					],
				]
			],
		];
	}
}