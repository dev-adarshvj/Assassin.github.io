<?php namespace RamonLeenders\BlockDesigner\FieldType\TextBoxFieldType;

defined('C5_EXECUTE') or die("Access Denied.");

use RamonLeenders\BlockDesigner\FieldType\FieldType;

class TextBoxFieldType extends FieldType
{
    protected $ftHandle = 'text_box';
    protected $dbType = 'C';
    protected $canRepeat = true;

    public function getFieldName()
    {
        return t("Text Box");
    }

    public function getFieldDescription()
    {
        return t("A text input field");
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
        return '<?php if (isset($' . $slug . ') && trim($' . $slug . ') != "") { ?>' . $this->data['prefix'] . '<?php echo ' . $inner . '; ?>' . $this->data['suffix'] . '<?php } ?>';
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
        $maxLength = $this->maxLength($this->data);
        $placeholder = isset($this->data['placeholder']) && trim($this->data['placeholder']) != '' ? h($this->data['placeholder']) : null;
        $fieldAttributes = [
            'maxlength' => $maxLength,
        ];
        if ($placeholder) {
            $fieldAttributes['placeholder'] = $placeholder;
        }
        $repeating = $this->getRepeating();
        $btFieldsRequired = $repeating ? '$btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']' : '$btFieldsRequired';
        if ($repeating && isset($this->data['parent'], $this->data['parent']['title_me']) && is_string($this->data['parent']['title_me']) && $this->data['row_id'] == (int)$this->data['parent']['title_me']) {
            $fieldAttributes['class'] = 'form-control title-me';
        }
        return '<div class="form-group">
    ' . parent::generateFormContent('label', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => $this->data['label'], 'description' => $this->data['description']], $repeating) . '
    ' . parent::generateFormContent('required', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'array' => $btFieldsRequired]) . '
    ' . parent::generateFormContent('text', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'attributes' => $fieldAttributes], $repeating) . '
</div>';
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
                'size' => $this->maxLength($this->data),
            ],
        ];
    }

    private function maxLength()
    {
        return isset($this->data['max_length']) && is_numeric($this->data['max_length']) && $this->data['max_length'] >= 1 && $this->data['max_length'] <= 255 ? (int)$this->data['max_length'] : 255;
    }
} 