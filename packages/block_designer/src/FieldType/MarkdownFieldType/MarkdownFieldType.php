<?php namespace RamonLeenders\BlockDesigner\FieldType\MarkdownFieldType;

defined('C5_EXECUTE') or die("Access Denied.");

use RamonLeenders\BlockDesigner\FieldType\FieldType;

class MarkdownFieldType extends FieldType
{
    protected $ftHandle = 'markdown';
    protected $dbType = 'X';
    protected $canRepeat = true;

    public function getFieldDescription()
    {
        return t("A simple text area where you can use all markdown functionalities");
    }

    public function getSearchableContent()
    {
        $repeating = $this->getRepeating();
        $repeatingTab = $repeating ? '    ' : null;
        $lines = [];
        if ($this->data['ft_count'] <= 0 && $this->data['ft_count_repeatable'] <= 0) {
            $lines[] = 'if (!class_exists(\'Parsedown\')) {
            ' . $repeatingTab . 'include_once(\'' . $this->data['btDirectory'] . 'libraries' . DIRECTORY_SEPARATOR . 'parsedown' . DIRECTORY_SEPARATOR . 'Parsedown.php' . '\');
        ' . $repeatingTab . '}';
        }
        if ($repeating) {
            $slug = '$' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"]';
            $lines[] = $repeatingTab . 'if (isset(' . $slug . ') && trim(' . $slug . ') != "") {';
            $lines[] = $repeatingTab . '    $content[] = (new Parsedown())->text(' . $slug . ');';
            $lines[] = $repeatingTab . '}';
        } else {
            $lines[] = '$content[] = (new Parsedown())->text($this->' . $this->data['slug'] . ');';
        }
        return implode(PHP_EOL . '        ', $lines);
    }

    public function getViewContents()
    {
        $slug = $this->getRepeating() ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '"]' : $this->data['slug'];
        return '<?php if (isset($' . $slug . ') && trim($' . $slug . ') != "") { ?>' . $this->data['prefix'] . '<?php echo (new Parsedown())->text($' . $slug . '); ?>' . $this->data['suffix'] . '<?php } ?>';
    }

    public function getViewFunctionContentsExtra()
    {
        if ($this->data['ft_count'] <= 0 && $this->data['ft_count_repeatable'] <= 0 && $this->getRepeating()) {
            return 'if (!class_exists(\'Parsedown\')) {
            include_once(\'' . $this->data['btDirectory'] . 'libraries' . DIRECTORY_SEPARATOR . 'parsedown' . DIRECTORY_SEPARATOR . 'Parsedown.php' . '\');
        }';
        }
    }

    public function getViewFunctionContents()
    {
        if ($this->data['ft_count'] <= 0 && $this->data['ft_count_repeatable'] <= 0 && !$this->getRepeating()) {
            return 'if (!class_exists(\'Parsedown\')) {
            include_once(\'' . $this->data['btDirectory'] . 'libraries' . DIRECTORY_SEPARATOR . 'parsedown' . DIRECTORY_SEPARATOR . 'Parsedown.php' . '\');
        }';
        }
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

    public function copyFiles()
    {
        $files = [];
        if ($this->data['ft_count'] <= 0) {
            $files[] = [
                'source' => $this->ftDirectory . 'libraries' . DIRECTORY_SEPARATOR,
                'target' => $this->data['btDirectory'] . 'libraries' . DIRECTORY_SEPARATOR,
            ];
        }
        return $files;
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