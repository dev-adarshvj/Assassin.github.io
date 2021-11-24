<?php namespace RamonLeenders\BlockDesigner\FieldType\TextAreaFieldType;

defined('C5_EXECUTE') or die("Access Denied.");

use RamonLeenders\BlockDesigner\FieldType\FieldType;

class TextAreaFieldType extends FieldType
{
    protected $ftHandle = 'text_area';
    protected $dbType = 'X';
    protected $canRepeat = true;

    public function getFieldDescription()
    {
        return t("A simple text area with no editing options");
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

    public function getViewContents()
    {
        $slug = $this->getRepeating() ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '"]' : $this->data['slug'];
        if (!isset($this->data['skip_h']) || !is_string($this->data['skip_h']) || $this->data['skip_h'] != '1') {
            $inner = 'h($' . $slug . ')';
        } else {
            $inner = '$' . $slug;
        }
        if (isset($this->data['nl2br']) && $this->data['nl2br'] == '1') {
            $inner = 'nl2br(' . $inner . ')';
        }
        return '<?php if (isset($' . $slug . ') && trim($' . $slug . ') != "") { ?>' . $this->data['prefix'] . '<?php echo ' . $inner . '; ?>' . $this->data['suffix'] . '<?php } ?>';
    }

    public function getValidateFunctionContents()
    {
        if ($this->getRepeating()) {
            return 'if (in_array("' . $this->data['slug'] . '", $this->btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']) && (!isset($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']) || trim($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']) == "")) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("' . h($this->data['label']) . '"), t("' . h($this->data['parent']['label']) . '"), $' . $this->data['parent']['slug'] . '_k));
                        }';
        } else {
            return 'if (in_array("' . $this->data['slug'] . '", $this->btFieldsRequired) && trim($args["' . $this->data['slug'] . '"]) == "") {
            $e->add(t("The %s field is required.", t("' . h($this->data['label']) . '")));
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

    public function getFieldOptions()
    {
        return parent::view('field_options.php');
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