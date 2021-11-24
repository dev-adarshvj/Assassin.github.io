<?php namespace RamonLeenders\BlockDesigner\FieldType\LinkFieldType;

defined('C5_EXECUTE') or die("Access Denied.");

use RamonLeenders\BlockDesigner\FieldType\FieldType;

class LinkFieldType extends FieldType
{
	protected $ftHandle = 'link';
	protected $dbType = 'I';
	protected $uses = ['Page'];
	protected $canRepeat = true;

	public function getFieldDescription()
	{
		return t("A page selector");
	}

	public function getViewContents()
	{
		$repeating = $this->getRepeating();
		$slug = $repeating ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '"]' : $this->data['slug'];
		$slugText = $repeating ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '_text"]' : $this->data['slug'] . '_text';
		$slugC = $repeating ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '_c"]' : $this->data['slug'] . '_c';
		return '<?php if (!empty($' . $slug . ') && ($' . $slugC . ' = Page::getByID($' . $slug . ')) && !$' . $slugC . '->error && !$' . $slugC . '->isInTrash()) {
    ?>' . $this->data['prefix'] . '<?php echo \'<a href="\' . $' . $slugC . '->getCollectionLink() . \'"' . (isset($this->data['class']) && is_string($this->data['class']) && trim($this->data['class']) != '' ? ' class="' . h($this->data['class']) . '"' : null) . '>\' . (isset($' . $slugText . ') && trim($' . $slugText . ') != "" ? $' . $slugText . ' : $' . $slugC . '->getCollectionName()) . \'</a>\';
?>' . $this->data['suffix'] . '<?php } ?>';
	}

	public function getValidateFunctionContents()
	{
		if ($this->getRepeating()) {
			$btFieldsRequired = '$this->btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']';
			$slug = '$' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']';
			return 'if ((in_array("' . $this->data['slug'] . '", ' . $btFieldsRequired . ') || (isset(' . $slug . ') && trim(' . $slug . ') != \'0\')) && (trim(' . $slug . ') == "" || (($page = Page::getByID(' . $slug . ')) && $page->error !== false))) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("' . h($this->data['label']) . '"), t("' . h($this->data['parent']['label']) . '"), $' . $this->data['parent']['slug'] . '_k));
                        }';
		} else {
			return 'if (in_array("' . $this->data['slug'] . '", $this->btFieldsRequired) && (trim($args["' . $this->data['slug'] . '"]) == "" || $args["' . $this->data['slug'] . '"] == "0" || (($page = Page::getByID($args["' . $this->data['slug'] . '"])) && $page->error !== false))) {
            $e->add(t("The %s field is required.", t("' . h($this->data['label']) . '")));
        }';
		}
	}

	public function getSaveFunctionContents()
	{
		if ($this->getRepeating()) {
			$return = 'if (isset($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\']) && trim($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\']) != \'\' && (($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '_c\'] = Page::getByID($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\'])) && !$' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '_c\']->error)) {
                    $data[\'' . $this->data['slug'] . '\'] = trim($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\']);
                } else {
                    $data[\'' . $this->data['slug'] . '\'] = null;
                }';
			if (!isset($this->data['hide_title']) || $this->data['hide_title'] != '1') {
				$return .= '
                if (isset($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '_text\']) && trim($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '_text\']) != \'\') {
                    $data[\'' . $this->data['slug'] . '_text\'] = trim($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '_text\']);
                } else {
                    $data[\'' . $this->data['slug'] . '_text\'] = null;
                }';
			}
			return $return;
		}
	}

	public function getFormContents()
	{
		$repeating = $this->getRepeating();
		$btFieldsRequired = $repeating ? '$btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']' : '$btFieldsRequired';
		$html = '<div class="form-group">
    ' . parent::generateFormContent('label', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => $this->data['label'], 'description' => $this->data['description']], $repeating) . '
    ' . parent::generateFormContent('required', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'array' => $btFieldsRequired], $repeating) . '
    ' . parent::generateFormContent('page_selector', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'attributes' => ['class' => 'link-ft']], $repeating) . '
</div>';
		if (!isset($this->data['hide_title']) || $this->data['hide_title'] != '1') {
			$html .= PHP_EOL . PHP_EOL . '<div class="form-group">
    ' . parent::generateFormContent('label', ['slug' => $this->data['slug'] . '_text', 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => $this->data['label'], 'suffix' => ' . " " . t("Text")'], $repeating) . '
    ' . parent::generateFormContent('text', ['slug' => $this->data['slug'] . '_text', 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null], $repeating) . '
</div>';
		}
		return $html;
	}

	public function getRepeatableUpdateItemJS()
	{
		if ($this->data['ft_count_repeatable'] <= 0) {
			return 'var pageSelector = $(newField).find(\'.link-ft\');
        if ($(pageSelector).length > 0) {
            $(pageSelector).each(function() {
                $(this).concretePageSelector({inputName: $(this).attr(\'data-input-name\'), cID : $(this).attr(\'data-cID\')});
            });
        }';
		}
	}

	public function getFieldOptions()
	{
		return parent::view('field_options.php');
	}

	public function getBtExportPageColumn()
	{
		return [$this->data['slug']];
	}

	public function getDbFields()
	{
		$fields = [
			0 => [
				'name' => $this->data['slug'],
				'type' => $this->getDbType(),
			],
			1 => [
				'name' => $this->data['slug'] . '_text',
				'type' => 'C',
			],
		];
		if (isset($this->data['hide_title']) && $this->data['hide_title'] == '1') {
			unset($fields[1]);
		}
		return $fields;
	}
}