<?php namespace RamonLeenders\BlockDesigner\FieldType\ColorPickerFieldType;

defined('C5_EXECUTE') or die("Access Denied.");

use RamonLeenders\BlockDesigner\FieldType\FieldType;

class ColorPickerFieldType extends FieldType
{
    protected $ftHandle = 'color_picker';
    protected $dbType = 'C';
    protected $canRepeat = true;

    public function getFieldName()
    {
        return t("Color Picker");
    }

    public function getFieldDescription()
    {
        return t("A color picker input field");
    }

    public function getViewContents()
    {
        $slug = $this->getRepeating() ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '"]' : $this->data['slug'];
        return '<?php if (isset($' . $slug . ') && trim($' . $slug . ') != "") { ?>' . $this->data['prefix'] . '<?php echo h($' . $slug . '); ?>' . $this->data['suffix'] . '<?php } ?>';
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

    public function getAssets()
    {
        return [
	        'addEdit' => [
		        'require' => [
			        [
				        'handle' => 'core/colorpicker',
			        ],
		        ],
	        ],
        ];
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

    private function getColorPickerConfig()
    {
        $palette = [];
        $config = [
            'color'                  => false,
            'appendTo'               => ".ui-dialog",
            'containerClassName'     => "",
            'replacerClassName'      => "",

            'flat'                   => isset($this->data['flat']) && $this->data['flat'] == '1' ? true : false,
            'showInput'              => isset($this->data['show_input']) && $this->data['show_input'] == '1' ? true : false,
            'allowEmpty'             => isset($this->data['allow_empty']) && $this->data['allow_empty'] == '1' ? true : false,
            'showButtons'            => isset($this->data['show_buttons']) && $this->data['show_buttons'] == '1' ? true : false,
            'clickoutFiresChange'    => isset($this->data['clickout_fires_change']) && $this->data['clickout_fires_change'] == '1' ? true : false,
            'showInitial'            => isset($this->data['show_initial']) && $this->data['show_initial'] == '1' ? true : false,
            'showPalette'            => isset($this->data['show_palette']) && $this->data['show_palette'] == '1' ? true : false,
            'showPaletteOnly'        => isset($this->data['show_palette_only']) && $this->data['show_palette_only'] == '1' ? true : false,
            'hideAfterPaletteSelect' => isset($this->data['hide_after_palette_select']) && $this->data['hide_after_palette_select'] == '1' ? true : false,
            'togglePaletteOnly'      => isset($this->data['toggle_palette_only']) && $this->data['toggle_palette_only'] == '1' ? true : false,
            'showSelectionPalette'   => isset($this->data['show_selection_palette']) && $this->data['show_selection_palette'] == '1' ? true : false,
            'localStorageKey'        => isset($this->data['local_storage']) && $this->data['local_storage'] == '1' ? $this->data['block_handle'] . '.' . $this->data['slug'] : false,
            'preferredFormat'        => isset($this->data['preferred_format']) && in_array($this->data['preferred_format'], ['hex', 'hex3', 'hsl', 'rgb', 'name']) ? $this->data['preferred_format'] : false,
            'showAlpha'              => isset($this->data['show_alpha']) && $this->data['show_alpha'] == '1' ? true : false,
            'disabled'               => isset($this->data['disabled']) && $this->data['disabled'] == '1' ? true : false,

            'maxSelectionSize'       => isset($this->data['max_selection_size']) && is_numeric($this->data['max_selection_size']) ? $this->data['max_selection_size'] : 7,
            'cancelText'             => isset($this->data['cancel_text']) && trim($this->data['cancel_text']) != '' ? $this->data['cancel_text'] : "cancel",
            'chooseText'             => isset($this->data['choose_text']) && trim($this->data['choose_text']) != '' ? $this->data['choose_text'] : "choose",
            'togglePaletteMoreText'  => isset($this->data['toggle_palette_more_text']) && trim($this->data['toggle_palette_more_text']) != '' ? $this->data['toggle_palette_more_text'] : "more",
            'togglePaletteLessText'  => isset($this->data['toggle_palette_less_text']) && trim($this->data['toggle_palette_less_text']) != '' ? $this->data['toggle_palette_less_text'] : "less",
            'clearText'              => isset($this->data['clear_text']) && trim($this->data['clear_text']) != '' ? $this->data['clear_text'] : "Clear Color Selection",
            'noColorSelectedText'    => isset($this->data['no_color_selected_text']) && trim($this->data['no_color_selected_text']) != '' ? $this->data['no_color_selected_text'] : "No Color Selected",
            'theme'                  => isset($this->data['theme']) && trim($this->data['theme']) != '' ? $this->data['theme'] : "sp-light",
            'selectionPalette'       => [],
            'offset'                 => null,

            'blockDesignerFunctions' => null,
        ];
        if (isset($this->data['palette']) && is_array($this->data['palette']) && !empty($this->data['palette'])) {
            foreach ($this->data['palette'] as $row => $values) {
                if (is_array($values)) {
                    $values = array_filter($values);
                    $values = array_unique($values);
                    if (!empty($values)) {
                        $values = array_values($values);
                        $palette[] = $values;
                    }
                }
            }
        }
        $config['palette'] = empty($palette) ? [["#ffffff", "#000000", "#ff0000", "#ff8000", "#ffff00", "#008000", "#0000ff", "#4b0082", "#9400d3"]] : $palette;
        $search = ['"blockDesignerFunctions":null'];
        $replace = [" hide: function(color) {
                   $('.sp-container').hide();
                },
                beforeShow: function(tinycolor) {
                    $('.sp-container').show();
                }"];
        $jsonArray = str_replace($search, $replace, json_encode($config));
        return $jsonArray;
    }

    public function getFormContents()
    {
        $repeating = $this->getRepeating();
        $btFieldsRequired = $repeating ? '$btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']' : '$btFieldsRequired';
        $fieldAttributes = [
	        'placeholder' => isset($this->data['placeholder']) && trim($this->data['placeholder']) != '' ? h($this->data['placeholder']) : null,
        ];
        $html = null;
        if (!$repeating) {
            $config = $this->getColorPickerConfig();
            $assets = '<script type="text/javascript">
    $(function () {
        $("#' . $this->data['slug'] . '").spectrum(' . $config . ');
    });
</script>';
            $html .= $assets;
        } else {
            $fieldAttributes['class'] = 'color_picker-ft';
        }
        $html .= '<div class="form-group">
    ' . parent::generateFormContent('label', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => $this->data['label'], 'description' => $this->data['description']], $repeating) . '
    ' . parent::generateFormContent('required', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'array' => $btFieldsRequired], $repeating) . '
    ' . parent::generateFormContent('text', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'attributes' => $fieldAttributes], $repeating) . '
</div>';
        return $html;
    }

    public function getRepeatableUpdateItemJS()
    {
        $config = $this->getColorPickerConfig();
        return 'var colorPicker = $(newField).find(\'.color_picker-ft\');
        if ($(colorPicker).length > 0) {
            $(colorPicker).spectrum(' . $config . ');
        }';
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
				'size' => 25,
			]
		];
	}
}