<?php namespace RamonLeenders\BlockDesignerPro\FieldType\FontAwesomeFieldType;

defined('C5_EXECUTE') or die("Access Denied.");

use RamonLeenders\BlockDesigner\FieldType\FieldType;
use Concrete\Core\File\Service\File;

class FontAwesomeFieldType extends FieldType
{
    protected $ftHandle = 'font_awesome';
    protected $dbType = 'C';
    protected $canRepeat = true;
    protected $pkgVersionRequired = '2.0.0';

    public function getFieldDescription()
    {
        return t("A Font Awesome icon select field");
    }

    public function getViewContents()
    {
        $slug = $this->getRepeating() ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '"]' : $this->data['slug'];
        return '<?php if (isset($' . $slug . ') && trim($' . $slug . ') != "") { ?>' . $this->data['prefix'] . '<i class="fa <?php echo $' . $slug . '; ?>"></i>' . $this->data['suffix'] . '<?php } ?>';
    }

    public function getValidateFunctionContents()
    {
        if ($this->getRepeating()) {
            return 'if ((!isset($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']) || trim($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']) == "")) {
                            if (in_array("' . $this->data['slug'] . '", $this->btFieldsRequired[\'' . $this->data['parent']['slug'] . '\'])) {
                                $e->add(t("The %s field is required (%s, row #%s).", t("' . h($this->data['label']) . '"), t("' . h($this->data['parent']['label']) . '"), $' . $this->data['parent']['slug'] . '_k));
                            }
                        } elseif (!array_key_exists($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\'], $this->fontAwesomeIcons(\'' . ($this->selectedFontAwesomeVersion()) . '\'))) {
                            $e->add(t("The %s field requires a valid Font Awesome icon  (%s, row #%s).", t("' . h($this->data['label']) . '"), t("' . h($this->data['parent']['label']) . '"), $' . $this->data['parent']['slug'] . '_k));
                        }';
        } else {
            return 'if (trim($args["' . $this->data['slug'] . '"]) == "") {
            if (in_array("' . $this->data['slug'] . '", $this->btFieldsRequired)) {
                $e->add(t("The %s field is required.", t("' . h($this->data['label']) . '")));
            }
        } elseif (!array_key_exists($args["' . $this->data['slug'] . '"], $this->fontAwesomeIcons(\'' . ($this->selectedFontAwesomeVersion()) . '\'))) {
            $e->add(t("The %s field requires a valid Font Awesome icon.", t("' . h($this->data['label']) . '")));
        }';
        }
    }

    public function getAddEditFunctionContents()
    {
        $repeating = $this->getRepeating();
        $btFieldsRequired = $repeating ? '$this->btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']' : '$this->btFieldsRequired';
        if ($repeating) {
            return '$' . $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '_options\'] = ' . ('(!in_array("' . $this->data['slug'] . '", ' . $btFieldsRequired . ') ? ["" => "-- " . t("None") . " --"] : [])') . ' + $this->fontAwesomeIcons(\'' . ($this->selectedFontAwesomeVersion()) . '\');';
        } else {
            return '$this->set(\'' . $this->data['slug'] . '_options\', ' . ('(!in_array("' . $this->data['slug'] . '", ' . $btFieldsRequired . ') ? ["" => "-- " . t("None") . " --"] : [])') . ' + $this->fontAwesomeIcons(\'' . ($this->selectedFontAwesomeVersion()) . '\'));';
        }
    }

    private function selectedFontAwesomeVersion()
    {
        return (isset($this->data['version']) ? str_replace('-', '.', $this->data['version']) : 4.2);
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
        $repeating = $this->getRepeating();
        $btFieldsRequired = $repeating ? '$btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']' : '$btFieldsRequired';
        $return = '';
        if (!$repeating) {
            $return .= PHP_EOL . '<script type="text/javascript">
    Concrete.event.publish(\'' . $this->data['block_handle'] . '.' . $this->data['slug'] . '.font_awesome\');
    $(document).ready(function () {
        $(\'select.font-awesome-previewed\').trigger(\'change\');
    });
</script>' . PHP_EOL . PHP_EOL;
        }
        $options = $repeating ? '$' . $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '_options\']' : '$' . $this->data['slug'] . '_options';
        $showPreview = isset($this->data['show_preview']) && $this->data['show_preview'] == '1' ? true : false;
        $attributes = ['class' => 'form-control font-awesome-previewed'];
        $return .= '<div class="form-group">
    ' . parent::generateFormContent('label', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => $this->data['label'], 'description' => $this->data['description']], $repeating) . '
    ' . parent::generateFormContent('required', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'array' => $btFieldsRequired]) . '
    ' . ($showPreview ? '<div class="font-awesome-group">' : null) . '
    ' . ($showPreview ? '    ' : null) . parent::generateFormContent('select', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'options' => $options, 'attributes' => $attributes], $repeating) . '
    ' . ($showPreview ? '    <i data-preview="icon" class=""></i>' : null) . '
    ' . ($showPreview ? '</div>' : null) . '
</div>';
        return $return;
    }

    public function getFieldOptions()
    {
        return parent::view('field_options.php', 'block_designer_pro');
    }

    public function getAutoCssContents()
    {
        if ($this->data['ft_count'] <= 0 && $this->data['ft_count_repeatable'] <= 0) {
            return '.font-awesome-group {margin-right: 35px;position: relative;}.font-awesome-group [data-preview="icon"] {position: absolute;right: -25px;top: 10px;}';
        }
    }

    public function getAutoJsContents()
    {
        if (!$this->getRepeating()) {
            return 'Concrete.event.bind(\'' . $this->data['block_handle'] . '.' . $this->data['slug'] . '.font_awesome\', function () {
    $(document).on(\'change\', \'select.font-awesome-previewed\', function (e) {
        var previewIcon = $(this).parent().find(\'[data-preview="icon"]\');
        if ($(previewIcon).length > 0) {
            var value = $(this).val();
            var classes = $.trim(value) != \'\' ? \'fa \' + value : \'\';
            $(previewIcon).removeAttr(\'class\');
            $(previewIcon).addClass(classes);
        }
    });
});';
        } else {
            return '$(container).on(\'change\', \'select.font-awesome-previewed\', function (e) {
        var previewIcon = $(this).parent().find(\'[data-preview="icon"]\');
        if ($(previewIcon).length > 0) {
            var value = $(this).val();
            var classes = $.trim(value) != \'\' ? \'fa \' + value : \'\';
            $(previewIcon).removeAttr(\'class\');
            $(previewIcon).addClass(classes);
        }
    });';
        }
    }

    public function getRepeatableUpdateItemJS()
    {
        return 'var fontAwesomeSelect = $(newField).find(\'select.font-awesome-previewed\');
        var previewIcon = $(fontAwesomeSelect).parent().find(\'[data-preview="icon"]\');
        if ($(previewIcon).length > 0) {
            var value = $(fontAwesomeSelect).val();
            var classes = $.trim(value) != \'\' ? \'fa \' + value : \'\';
            $(previewIcon).removeAttr(\'class\');
            $(previewIcon).addClass(classes);
        }';
    }

	public function getDbFields()
	{
		return [
			[
				'name' => $this->data['slug'],
				'type' => $this->getDbType(),
				'size' => 100,
			]
		];
	}

    public function getExtraFunctionsContents()
    {
        if ($this->data['ft_count'] > 0) {
            return;
        }
        $fileService = new File();
        return $fileService->getContents($this->ftDirectory . 'elements' . DIRECTORY_SEPARATOR . 'extra_functions.txt');
    }
}