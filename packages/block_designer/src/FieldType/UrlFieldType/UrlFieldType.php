<?php namespace RamonLeenders\BlockDesigner\FieldType\UrlFieldType;

defined('C5_EXECUTE') or die("Access Denied.");

use RamonLeenders\BlockDesigner\FieldType\FieldType;

class UrlFieldType extends FieldType
{
    protected $ftHandle = 'url';
    protected $dbType = 'C';
    protected $canRepeat = true;

    public function getFieldName()
    {
        return t("URL");
    }

    public function getFieldDescription()
    {
        return t("A text input field, where you would enter an http:// URL");
    }

    public function getViewContents()
    {
        $newWindow = isset($this->data['url_target']) && is_string($this->data['url_target']) && $this->data['url_target'] == '1' ? true : false;
        $repeating = $this->getRepeating();
        $slug = $repeating ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '"]' : $this->data['slug'];
        $slugText = $repeating ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '_text' . '"]' : $this->data['slug'] . '_text';
        $inner = !isset($this->data['hide_title']) || $this->data['hide_title'] != '1' ? '" . (isset($' . $slugText . ') && trim($' . $slugText . ') != "" ? $' . $slugText . ' : $' . $slug . ') . "' : null;
        return '<?php if (isset($' . $slug . ') && trim($' . $slug . ') != "") { ?>' . $this->data['prefix'] . '<?php echo "<a href=\"" . $' . $slug . ' . "\"' . ($newWindow ? ' target=\"_blank\"' : null) . (isset($this->data['class']) && is_string($this->data['class']) && trim($this->data['class']) != '' ? ' class=\"' . h($this->data['class']) . '\"' : null) . '>' . $inner . '</a>"; ?>' . $this->data['suffix'] . '<?php } ?>';
    }

    public function getValidateFunctionContents()
    {
        if ($this->getRepeating()) {
            return 'if (((!in_array("' . $this->data['slug'] . '", $this->btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']) && isset($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']) && trim($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']) != "") || (in_array("' . $this->data['slug'] . '", $this->btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']))) && (!filter_var($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\'], FILTER_VALIDATE_URL))) {
                            $e->add(t("The %s field does not have a valid URL (%s, row #%s).", t("' . h($this->data['label']) . '"), t("' . h($this->data['parent']['label']) . '"), $' . $this->data['parent']['slug'] . '_k));
                        }';
        } else {
            return 'if (((!in_array("' . $this->data['slug'] . '", $this->btFieldsRequired) && trim($args["' . $this->data['slug'] . '"]) != "") || (in_array("' . $this->data['slug'] . '", $this->btFieldsRequired))) && !filter_var($args["' . $this->data['slug'] . '"], FILTER_VALIDATE_URL)) {
            $e->add(t("The %s field does not have a valid URL.", t("' . h($this->data['label']) . '")));
        }';
        }
    }

    public function getSaveFunctionContents()
    {
        if ($this->getRepeating()) {
            $return = 'if (isset($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\']) && trim($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\']) != \'\') {
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
        $return = '<div class="form-group">
    ' . parent::generateFormContent('label', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => $this->data['label'], 'description' => $this->data['description']], $repeating) . '
    ' . parent::generateFormContent('required', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'array' => $btFieldsRequired], $repeating) . '
    ' . parent::generateFormContent('text', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null], $repeating) . '
</div>';
        if (!isset($this->data['hide_title']) || $this->data['hide_title'] != '1') {
            $return .= PHP_EOL . PHP_EOL . '<div class="form-group">
    ' . parent::generateFormContent('label', ['slug' => $this->data['slug'] . '_text', 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => $this->data['label'], 'suffix' => ' . " " . t(\'Text\')'], $repeating) . '
    ' . parent::generateFormContent('text', ['slug' => $this->data['slug'] . '_text', 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null], $repeating) . '
</div>';
        }
        return $return;
    }

    public function getFieldOptions()
    {
        return parent::view('field_options.php');
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
                'type' => $this->getDbType(),
            ],
        ];
        if (isset($this->data['hide_title']) && $this->data['hide_title'] == '1') {
            unset($fields[1]);
        }
        return $fields;
    }
}