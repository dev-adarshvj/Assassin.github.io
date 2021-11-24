<?php namespace RamonLeenders\BlockDesignerPro\FieldType\BooleanFieldType;

defined('C5_EXECUTE') or die("Access Denied.");

use RamonLeenders\BlockDesigner\FieldType\FieldType;

class BooleanFieldType extends FieldType
{
    protected $ftHandle = 'boolean';
    protected $dbType = 'C';
    protected $canRepeat = true;
    protected $pkgVersionRequired = '1.2.4';

    public function getFieldName()
    {
        return t("Boolean");
    }

    public function getFieldDescription()
    {
        return t("A boolean field");
    }

    public function getViewContents()
    {
        $yes = isset($this->data['yes_value']) && trim($this->data['yes_value']) != '' ? '"' . h($this->data['yes_value']) . '"' : 't("Yes")';
        $no = isset($this->data['no_value']) && trim($this->data['no_value']) != '' ? '"' . h($this->data['no_value']) . '"' : 't("No")';
        $slug = $this->getRepeating() ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '"]' : $this->data['slug'];
        return '<?php if (isset($' . $slug . ') && trim($' . $slug . ') != "") { ?>' . $this->data['prefix'] . '<?php echo $' . $slug . ' == 1 ? ' . $yes . ' : ' . $no . '; ?>' . $this->data['suffix'] . '<?php } ?>';
    }

    public function getViewFunctionContents()
    {
        if ($this->getRepeating()) {
            if (isset($this->data['fallback_value']) && trim($this->data['fallback_value']) != '') {
                return 'if (!isset($' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"]) || trim($' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"]) == "") {
                $' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"] = \'' . h($this->data['fallback_value']) . '\';
            }';
            }
        } else {
            if (isset($this->data['fallback_value']) && trim($this->data['fallback_value']) != '') {
                return 'if (trim($this->' . $this->data['slug'] . ') == "") {
            $this->set("' . $this->data['slug'] . '", \'' . h($this->data['fallback_value']) . '\');
        }';
            }
        }
        return;
    }

    public function getValidateFunctionContents()
    {
        if ($this->getRepeating()) {
            $slug = '$' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']';
            return 'if (in_array("' . $this->data['slug'] . '", $this->btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']) && (!isset(' . $slug . ') || trim(' . $slug . ') == "" || !in_array(' . $slug . ', [0, 1]))) {
                            $e->add(t("The %s field is required.", t("' . h($this->data['label']) . '"), t("' . h($this->data['parent']['label']) . '"), $' . $this->data['parent']['slug'] . '_k));
                        }';
        } else {
            return 'if (in_array("' . $this->data['slug'] . '", $this->btFieldsRequired) && (trim($args["' . $this->data['slug'] . '"]) == "" || !in_array($args["' . $this->data['slug'] . '"], [0, 1]))) {
            $e->add(t("The %s field is required.", t("' . h($this->data['label']) . '")));
        }';
        }
    }

    public function getAddFunctionContents()
    {
        if (isset($this->data['default_value']) && trim($this->data['default_value']) != '' && in_array($this->data['default_value'], [0, 1])) {
            if (!$this->getRepeating()) {
	            return '$this->set("' . $this->data['slug'] . '", ' . $this->data['default_value'] . ');';
            }
        }
    }

    public function getFormContents()
    {
        $yes = isset($this->data['yes_label']) && trim($this->data['yes_label']) != '' ? '"' . h($this->data['yes_label']) . '"' : 't("Yes")';
        $no = isset($this->data['no_label']) && trim($this->data['no_label']) != '' ? '"' . h($this->data['no_label']) . '"' : 't("No")';
        $repeating = $this->getRepeating();
	    $selectAttributes = [];
	    if($repeating && isset($this->data['default_value']) && trim($this->data['default_value']) != '' && in_array($this->data['default_value'], [0, 1])){
		    $selectAttributes['data-attr-default-value'] = $this->data['default_value'];
	    }
        $btFieldsRequired = $repeating ? '$btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']' : '$btFieldsRequired';
        return '<div class="form-group">
    ' . parent::generateFormContent('label', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => $this->data['label'], 'description' => $this->data['description']], $repeating) . '
    ' . parent::generateFormContent('required', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'array' => '$btFieldsRequired'], $repeating) . '
    ' . parent::generateFormContent('select', ['attributes' => $selectAttributes, 'slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'options' => '(isset(' . $btFieldsRequired . ') && in_array(\'' . $this->data['slug'] . '\', ' . $btFieldsRequired . ') ? [] : ["" => "--" . t("Select") . "--"]) + [0 => ' . $no . ', 1 => ' . $yes . ']'], $repeating) . '
</div>';
    }

    public function getSaveFunctionContents()
    {
        if ($this->getRepeating()) {
            return 'if (isset($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\']) && trim($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\']) != \'\' && in_array($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\'], [0, 1])) {
                    $data[\'' . $this->data['slug'] . '\'] = $' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\'];
                } else {
                    $data[\'' . $this->data['slug'] . '\'] = null;
                }';
        }
        else {
            return 'if (!isset($args["' . $this->data['slug'] . '"]) || trim($args["' . $this->data['slug'] . '"]) == "" || !in_array($args["' . $this->data['slug'] . '"], [0, 1])) {
            $args["' . $this->data['slug'] . '"] = \'\';
        }';
        }
    }

    public function getFieldOptions()
    {
        return parent::view('field_options.php', 'block_designer_pro');
    }

	public function getDbFields()
	{
		return [
			[
				'name'       => $this->data['slug'],
				'type'       => $this->getDbType(),
				'size'       => 1,
				'attributes' => [
					'unsigned' => true,
				],
			]
		];
	}
}