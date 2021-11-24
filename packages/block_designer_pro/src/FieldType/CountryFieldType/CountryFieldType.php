<?php namespace RamonLeenders\BlockDesignerPro\FieldType\CountryFieldType;

defined('C5_EXECUTE') or die("Access Denied.");

use RamonLeenders\BlockDesigner\FieldType\FieldType;
use Core;

class CountryFieldType extends FieldType
{
    protected $ftHandle = 'country';
    protected $dbType = 'C';
    protected $uses = ['Core'];
    protected $canRepeat = true;
    protected $appVersionRequired = '5.7.3.1';
    protected $pkgVersionRequired = '2.0.0';

    public function getFieldDescription()
    {
        return t("A country select field");
    }

    public function getViewContents()
    {
        $slug = $this->getRepeating() ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '"]' : $this->data['slug'];
        $output = isset($this->data['output_lang_code']) && $this->data['output_lang_code'] == '1' ? '$' . $slug : 'Core::Make(\'lists/countries\')->getCountryName($' . $slug . ')';
        return '<?php if (isset($' . $slug . ') && trim($' . $slug . ') != "") { ?>' . $this->data['prefix'] . '<?php echo ' . $output . '; ?>' . $this->data['suffix'] . '<?php } ?>';
    }

    public function getViewFunctionContentsExtra()
    {
        return $this->getCountriesLines();
    }

    public function getViewFunctionContents()
    {
        $countries = Core::Make('lists/countries')->getCountries();
        $repeating = $this->getRepeating();
        $return = $repeating ? null : $this->getCountriesLines();
        if (isset($this->data['fallback_value']) && trim($this->data['fallback_value']) != '' && array_key_exists($this->data['fallback_value'], $countries)) {
            if ($repeating) {
                $return .= 'if (isset($' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"]) && trim($' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"]) == "" && array_key_exists("' . $this->data['fallback_value'] . '", $' . $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '_countries\'])) {
                $' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"] = \'' . $this->data['fallback_value'] . '\';
            } elseif (!isset($' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"]) || !array_key_exists($' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"], $' . $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '_countries\'])) {
                $' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"] = false;
            }';
            } else {
                $return .= 'if (trim($this->' . $this->data['slug'] . ') == "" && array_key_exists("' . $this->data['fallback_value'] . '", $' . $this->data['slug'] . '_countries)) {
            $this->set("' . $this->data['slug'] . '", \'' . $this->data['fallback_value'] . '\');
        } elseif (!array_key_exists($this->' . $this->data['slug'] . ', $' . $this->data['slug'] . '_countries)) {
            $this->set("' . $this->data['slug'] . '", false);
        }';
            }
        } else {
            if ($repeating) {
                $return .= 'if (!isset($' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"]) || !array_key_exists($' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"], $' . $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '_countries\'])) {
                $' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"] = false;
            }';
            } else {
                $return .= '
        if (!array_key_exists($this->' . $this->data['slug'] . ', $' . $this->data['slug'] . '_countries)) {
            $this->set("' . $this->data['slug'] . '", false);
        }';
            }
        }
        return $return;
    }

    public function getAddEditFunctionContents()
    {
        if ($this->getRepeating()) {
            $return = $this->getCountriesLines();
            $return .= PHP_EOL . '        ' . 'if (!in_array("' . $this->data['slug'] . '", $this->btFieldsRequired[\'' . $this->data['parent']['slug'] . '\'])) {
            $' . $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '_countries\'] = [\'\' => \'-- \' . t(\'None\') . \' --\'] + $' . $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '_countries\'];
        }';
            return $return;
        } else {
            $return = $this->getCountriesLines();
            if (!$this->data['required']) {
                $return .= PHP_EOL . '        $' . $this->data['slug'] . '_countries = [\'\' => \'-- \' . t(\'None\') . \' --\'] + $' . $this->data['slug'] . '_countries;';
            }
            $return .= PHP_EOL . '        $this->set("' . $this->data['slug'] . '_countries", $' . $this->data['slug'] . '_countries);';
            return $return;
        }
    }

    private function getCountriesLines()
    {
        $repeating = $this->getRepeating();
        $slugCountries = $repeating ? $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '_countries\']' : $this->data['slug'] . '_countries';
        $slugExcludedCounties = $repeating ? $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '_excludedCountries\']' : $this->data['slug'] . '_excludedCountries';
        $slugExcludedCounty = $this->data['slug'] . '_excludedCountry';
        $export = '[]';
        if(isset($this->data['excludes']) && is_array($this->data['excludes']) && !empty($this->data['excludes'])){
            $patterns = ['(\d+\s=>)', "/\s+/", "/\s([?.!])/", '/,\)/', '/\',\'/', '/=>/', '/\),\'/'];
            $replacer = ['', '', '$1', ')', "', '", ' => ', '), \''];
            $export = preg_replace($patterns, $replacer, var_export($this->data['excludes'], true));
        }
        return '$' . $slugCountries . ' = Core::Make(\'lists/countries\')->getCountries();
        $' . $slugExcludedCounties . ' = ' . $export . ';
        if (!empty($' . $slugExcludedCounties . ')) {
            foreach ($' . $slugExcludedCounties . ' as $' . $slugExcludedCounty . ') {
                if (isset($' . $slugCountries . '[$' . $slugExcludedCounty . '])) {
                    unset($' . $slugCountries . '[$' . $slugExcludedCounty . ']);
                }
            }
        }';
    }

    public function getValidateFunctionContentsExtra()
    {
        return $this->getCountriesLines();
    }

    public function getValidateFunctionContents()
    {
        if ($this->getRepeating()) {
            return 'if (in_array("' . $this->data['slug'] . '", $this->btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']) && (!isset($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']) || trim($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']) == "")) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("' . h($this->data['label']) . '"), t("' . h($this->data['parent']['label']) . '"), $' . $this->data['parent']['slug'] . '_k));
                        } elseif (trim($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']) != "" && !array_key_exists($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\'], $' . $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '_countries\'])) {
                            $e->add(t("The %s field does not contain an available country (%s, row #%s).", t("' . h($this->data['label']) . '"), t("' . h($this->data['parent']['label']) . '"), $' . $this->data['parent']['slug'] . '_k));
                        }';
        } else {
            return $this->getCountriesLines() . '
        if (in_array("' . $this->data['slug'] . '", $this->btFieldsRequired) && trim($args["' . $this->data['slug'] . '"]) == "") {
            $e->add(t("The %s field is required.", t("' . h($this->data['label']) . '")));
        } elseif (trim($args["' . $this->data['slug'] . '"]) != "" && !array_key_exists($args["' . $this->data['slug'] . '"], $' . $this->data['slug'] . '_countries)) {
            $e->add(t("The %s field does not contain an available country.", t("' . h($this->data['label']) . '")));
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
        $repeating = $this->getRepeating();
        $btFieldsRequired = $repeating ? '$btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']' : '$btFieldsRequired';
        $counties = $repeating ? '$' . $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '_countries\']' : '$' . $this->data['slug'] . '_countries';
        return '<div class="form-group">
    ' . parent::generateFormContent('label', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => $this->data['label'], 'description' => $this->data['description']], $repeating) . '
    ' . parent::generateFormContent('required', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'array' => $btFieldsRequired], $repeating) . '
    ' . parent::generateFormContent('select', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'options' => $counties], $repeating) . '
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
				'size' => 2,
			]];
	}
}