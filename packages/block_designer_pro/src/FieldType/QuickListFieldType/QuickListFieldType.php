<?php namespace RamonLeenders\BlockDesignerPro\FieldType\QuickListFieldType;

defined('C5_EXECUTE') or die("Access Denied.");

use RamonLeenders\BlockDesigner\FieldType\FieldType;

class QuickListFieldType extends FieldType
{
    protected $ftHandle = 'quick_list';
    protected $dbType = 'X';
    protected $canRepeat = true;
    protected $pkgVersionRequired = '1.2.4';

    public function getFieldName()
    {
        return t("Quick List");
    }

    public function getFieldDescription()
    {
        return t("A simple text area where a simple return will separate the list items.");
    }

    public function getSearchableContent()
    {
        $repeating = $this->getRepeating();
        $repeatingTab = $repeating ? '    ' : null;
        $slug = $repeating ? '$' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"]' : '$this->' . $this->data['slug'];
        if ($repeating) {
            $slug = 'isset(' . $slug . ') && trim(' . $slug . ') != "" ? ' . $slug . ' : null';
        }
        return '$' . $this->data['slug'] . '_optionsExploded = explode("\n", ' . $slug . ');
        '. $repeatingTab . '$' . $this->data['slug'] . '_options = [];
        '. $repeatingTab . 'foreach ($' . $this->data['slug'] . '_optionsExploded as $' . $this->data['slug'] . '_option) {
            '. $repeatingTab . '$' . $this->data['slug'] . '_options[] = \'<li>\' . $' . $this->data['slug'] . '_option . \'</li>\';
        '. $repeatingTab . '}
        '. $repeatingTab . 'if (!empty($' . $this->data['slug'] . '_options)) {
            '. $repeatingTab . '$content[] = \'<ul>\' . implode(\'\', $' . $this->data['slug'] . '_options) . \'</ul>\';
        '. $repeatingTab . '}';
    }

    public function getViewContents()
    {
        $slug = $this->getRepeating() ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '_options"]' : $this->data['slug'] . '_options';
        $tag = isset($this->data['list_type']) && in_array($this->data['list_type'], ['ul', 'ol']) ? $this->data['list_type'] : 'ul';
        return '<?php if (isset($' . $slug . ') && !empty($' . $slug . ')) { ?>' . $this->data['prefix'] . '<' . $tag . '><?php foreach ($' . $slug . ' as $' . $this->data['slug'] . '_option) {
            echo \'<li>\' . $' . $this->data['slug'] . '_option . \'</li>\';
        } ?></' . $tag . '>' . $this->data['suffix'] . '<?php } ?>';
    }

    public function getViewFunctionContents()
    {
        if ($this->getRepeating()) {
            return '$' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '_options"] = isset($' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"]) && trim($' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"]) != "" ? explode("\n", $' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"]) : [];';
        } else {
            return '$this->set("' . $this->data['slug'] . '_options", trim($this->' . $this->data['slug'] . ') != "" ? explode("\n", $this->' . $this->data['slug'] . ') : []);';
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

    public function getFormContents()
    {
        $fieldAttributes = ['rows' => 5];
        $repeating = $this->getRepeating();
        $btFieldsRequired = $repeating ? '$btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']' : '$btFieldsRequired';
        return '<div class="form-group">
    ' . parent::generateFormContent('label', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => $this->data['label'], 'description' => $this->data['description']], $repeating) . '
    ' . parent::generateFormContent('required', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'array' => $btFieldsRequired], $repeating) . '
    ' . parent::generateFormContent('textarea', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'attributes' => $fieldAttributes], $repeating) . '
</div>';
    }

    public function getFieldOptions()
    {
        return parent::view('field_options.php', 'block_designer_pro');
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
}