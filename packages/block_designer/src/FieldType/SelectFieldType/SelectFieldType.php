<?php namespace RamonLeenders\BlockDesigner\FieldType\SelectFieldType;

defined('C5_EXECUTE') or die("Access Denied.");

use RamonLeenders\BlockDesigner\FieldType\FieldType;

class SelectFieldType extends FieldType
{
    protected $ftHandle = 'select';
    protected $dbType = 'C';
    protected $canRepeat = true;

    public function getFieldDescription()
    {
        return t("A field with multiple options (also known as dropdown), where you need to pick one");
    }

    private function _options()
    {
        $options = [];
        if (isset($this->data['select_options']) && trim($this->data['select_options']) != '') {
            $options_exploded = explode("\n", $this->data['select_options']);
            $max_key = 0;
            foreach ($options_exploded as $option_exploded) {
                list($before, $after) = explode(' :: ', $option_exploded, 2);
                if (trim($after) != '') {
                    $key = strip_tags($before);
                    $key_no = 0;
                    while (array_key_exists($key, $options)) {
                        $key_no++;
                        $key = $before . '_' . $key_no;
                    }
                    if (is_numeric($key) && $key > $max_key) {
                        $max_key = $key;
                    }
                    $options[$key] = h(trim($after));
                } else {
                    $max_key++;
                    $options[$max_key] = h(trim($before));
                }
            }
        }
        return $options;
    }

    public function validate()
    {
        $options = $this->_options();
        return !empty($options) ? true : t('No select choices were entered for row #%s.', $this->data['row_id']);
    }

    public function getViewFunctionContents()
    {
        $optionsArray = $this->buildOptions();
        if (!$this->getRepeating()) {
            return '$' . $this->data['slug'] . '_options = [' . PHP_EOL . '    ' . implode(',' . PHP_EOL . '    ', $optionsArray) . PHP_EOL . '    ' . '    ];
        $this->set("' . $this->data['slug'] . '_options", $' . $this->data['slug'] . '_options);';
        }
    }

    public function getViewFunctionContentsExtra()
    {
        $optionsArray = $this->buildOptions();
        if ($this->getRepeating()) {
            return '$' . $this->data['parent']['slug'] . '["' . $this->data['slug'] . '_options"] = [' . PHP_EOL . '    ' . implode(',' . PHP_EOL . '    ', $optionsArray) . PHP_EOL . '    ' . '    ];';
        }
    }

    public function getViewContents()
    {
        $repeating = $this->getRepeating();
        $slug = $repeating ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '"]' : $this->data['slug'];
        if (isset($this->data['view_output']) && trim($this->data['view_output']) != '' && in_array($this->data['view_output'], [1, 2])) {
            $slugOptions = $repeating ? $this->data['parent']['slug'] . '["' . $this->data['slug'] . '_options"]' : $this->data['slug'] . '_options';
            switch ($this->data['view_output']) {
                case 1:
                    $inner = '$' . $slug;
                    break;
                case 2:
                    $inner = '$' . $slugOptions . '[$' . $slug . ']';
                    break;
            }
            return '<?php if (isset($' . $slug . ') && trim($' . $slug . ') != "" && array_key_exists($' . $slug . ', $' . $slugOptions . ')) { ?>' . $this->data['prefix'] . '<?php echo ' . $inner . '; ?>' . $this->data['suffix'] . '<?php } ?>';
        } else {
            $options = $this->_options();
            $cases = '';
            foreach ($options as $option_key => $option_value) {
                $options[$option_key] = $option_value;
                $cases .= '
case "' . $option_key . '":
    // ENTER MARKUP HERE FOR FIELD "' . h($this->data['label']) . '" : CHOICE "' . $options[$option_key] . '"
    break;';
            }
            return '<?php if (trim($' . $slug . ') != "") { ?>' . $this->data['prefix'] . '
<?php switch($' . $slug . ') {' . $cases . '
                                } ?>' . $this->data['suffix'] . '<?php } ?>';
        }
    }

    public function getSaveFunctionContents()
    {
        if ($this->getRepeating()) {
            return 'if (isset($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\']) && trim($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\']) != \'\') {
                    $data[\'' . $this->data['slug'] . '\'] = trim($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\']);
                } else {
                    $data[\'' . $this->data['slug'] . '\'] = null;
                }';
        }
    }

    public function getValidateFunctionContents()
    {
        $options = $this->_options();
        $key_options = [];
        foreach ($options as $option_key => $option_value) {
            $key_options[] = '"' . $option_key . '"';
        }
        if ($this->getRepeating()) {
            return 'if ((in_array("' . $this->data['slug'] . '", $this->btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']) && (!isset($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']) || trim($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']) == "")) || (isset($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']) && trim($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']) != "" && !in_array($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\'], [' . implode(', ', $key_options) . ']))) {
                            $e->add(t("The %s field has an invalid value (%s, row #%s).", t("' . h($this->data['label']) . '"), t("' . h($this->data['parent']['label']) . '"), $' . $this->data['parent']['slug'] . '_k));
                        }';
        } else {
            return 'if ((in_array("' . $this->data['slug'] . '", $this->btFieldsRequired) && (!isset($args["' . $this->data['slug'] . '"]) || trim($args["' . $this->data['slug'] . '"]) == "")) || (isset($args["' . $this->data['slug'] . '"]) && trim($args["' . $this->data['slug'] . '"]) != "" && !in_array($args["' . $this->data['slug'] . '"], [' . implode(', ', $key_options) . ']))) {
            $e->add(t("The %s field has an invalid value.", t("' . h($this->data['label']) . '")));
        }';
        }
    }

    private function buildOptions()
    {
        $options = $this->_options();
        $optionsArray = [];
        $translate = isset($this->data['translate']) && $this->data['translate'] == '1' ? true : false;
        foreach ($options as $key => $option) {
            $key = trim($key) == '' ? "''" : "'$key'";
            if ($translate) {
                $optionsArray[] = '        ' . $key . ' => t("' . trim($option) . '")';
            } else {
                $optionsArray[] = '        ' . $key . ' => "' . trim($option) . '"';
            }
        }
        if (!$this->data['required']) {
            $optionsArray = array_merge(["        ''" . ' => "-- " . t("None") . " --"'], $optionsArray);
        }
        return $optionsArray;
    }

    public function getFormContents()
    {
        $repeating = $this->getRepeating();
        $btFieldsRequired = $repeating ? '$btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']' : '$btFieldsRequired';
        $slugOptions = $repeating ? $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '_options\']' : $this->data['slug'] . '_options';
	    $selectAttributes = [];
	    if($repeating && isset($this->data['default_value']) && trim($this->data['default_value']) != '' && in_array($this->data['default_value'], [0, 1])){
		    $selectAttributes['data-attr-default-value'] = $this->data['default_value'];
	    }
	    return '<div class="form-group">
    ' . parent::generateFormContent('label', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => $this->data['label'], 'description' => $this->data['description']], $repeating) . '
    ' . parent::generateFormContent('required', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'array' => $btFieldsRequired], $repeating) . '
    ' . parent::generateFormContent('select', ['attributes' => $selectAttributes, 'slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'options' => '$' . $slugOptions], $repeating) . '
</div>';
    }

    public function getAddEditFunctionContents()
    {
        $optionsArray = $this->buildOptions();
        if ($this->getRepeating()) {
            return '$' . $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '_options\'] = [' . PHP_EOL . '    ' . implode(',' . PHP_EOL . '    ', $optionsArray) . PHP_EOL . '    ' . '    ];';
        } else {
	        return '$this->set("' . $this->data['slug'] . '_options", [' . PHP_EOL . '        ' . implode(',' . PHP_EOL . '        ', $optionsArray) . PHP_EOL . '            ]
        );';
        }
    }

    public function getAddFunctionContents()
    {
        if (!$this->getRepeating()) {
	        if (isset($this->data['default_value']) && trim($this->data['default_value']) != '' && array_key_exists($this->data['default_value'], $this->_options())) {
		        return '$this->set("' . $this->data['slug'] . '", \'' . h($this->data['default_value']) . '\');';
	        }
        }
    }

    public function getFieldOptions()
    {
        return parent::view('field_options.php');
    }

    public function getDbFields()
    {
        $dbFields = [
            0 => [
                'name' => $this->data['slug'],
                'type' => $this->getDbType(),
            ]
        ];
        if ($this->data['required']) {
            $dbFields[0]['default'] = '0';
        }
        return $dbFields;
    }
}