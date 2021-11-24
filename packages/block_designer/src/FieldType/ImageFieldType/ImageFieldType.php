<?php namespace RamonLeenders\BlockDesigner\FieldType\ImageFieldType;

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Asset\AssetList;
use RamonLeenders\BlockDesigner\FieldType\FieldType;

class ImageFieldType extends FieldType
{
    protected $ftHandle = 'image';
    protected $dbType = 'I';
    protected $uses = ['File', 'Page'];
    protected $canRepeat = true;

    public function getFieldDescription()
    {
        return t("An image selector");
    }

    public function validate()
    {
        $errors = [];
        if (isset($this->data['thumbnail']) && $this->data['thumbnail'] == '1') {
            $values = [
                'height' => t('Height'),
                'width'  => t('Width'),
            ];
            foreach ($values as $key => $value) {
                if (isset($this->data[$key]) && trim($this->data[$key]) != '') {
                    $integer = (int)$this->data[$key];
                    if ($integer <= 0) {
                        $errors[] = t('The %s for the image on row #%s has to be higher than 0.', $value, $this->data['row_id']);
                    } else {
                        if (!ctype_digit($this->data[$key])) {
                            $errors[] = t('The %s for the image on row #%s has to be a numeric value (floating numbers disallowed).', $value, $this->data['row_id']);
                        }
                    }
                } else {
                    $errors[] = t('No %s for the image on row #%s has been entered.', $value, $this->data['row_id']);
                }
            }
        }
        return empty($errors) ? true : implode('<br/>', $errors);
    }

    private function generateImage($field_data = [])
    {
        $slug = $this->getRepeating() ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '"]' : $this->data['slug'];
        $slugThumb = $this->getRepeating() ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '_thumb"]' : $this->data['slug'] . '_thumb';
        $slugTag = $this->getRepeating() ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '_tag"]' : $this->data['slug'] . '_tag';
        if (isset($this->data['thumbnail']) && $this->data['thumbnail'] == '1') {
            $width = (int)$this->data['width'];
            $height = (int)$this->data['height'];
            $crop = isset($this->data['crop']) && $this->data['crop'] == '1' ? true : false;
            $field_data['src'] = '$' . $slugThumb . '->src';
            $imgThumb = '$' . $slugThumb . ' = Core::make(\'helper/image\')->getThumbnail($' . $slug . ', ' . $width . ', ' . $height . ', ' . var_export($crop, true) . ')';
        }
        if (isset($this->data['output_src_only']) && $this->data['output_src_only'] == '1') {
            if (isset($this->data['thumbnail']) && $this->data['thumbnail'] == '1') {
                return '<?php if (' . $imgThumb . ') {
                                echo ' . $field_data['src'] . ';
                            } ?>';
            } else {
                return '<?php echo ' . $field_data['src'] . '; ?>';
            }
        } else {
            if (isset($this->data['responsive_image']) && $this->data['responsive_image'] == '1') {
                $lines = ['<?php'];
                if (isset($this->data['thumbnail']) && $this->data['thumbnail'] == '1') {
                    $lines[] = '$' . $slugThumb . ' = Core::make(\'helper/image\')->getThumbnail($' . $slug . ', ' . $width . ', ' . $height . ', ' . var_export($crop, true) . ');
                                $' . $slugTag . ' = new \HtmlObject\Image();
                                $' . $slugTag . '->src($' . $slugThumb . '->src)->width($' . $slugThumb . '->width)->height($' . $slugThumb . '->height);';
                } else {
	                $lines[] = '$' . $slugTag . ' = Core::make(\'html/image\', [$' . $slug . '])->getTag();';
	                if (isset($this->data['thumbnail_handle']) && trim($this->data['thumbnail_handle']) != '') {
		                $lines[] = '$' . $slugTag . '->src(' . $field_data['src'] . ');';
	                }
                }
                $lines[] = '$' . $slugTag . '->alt(' . $field_data['alt'] . ');';
                if (isset($field_data['class']) && trim($field_data['class']) != '') {
                    $lines[] = '$' . $slugTag . '->addClass(\'' . $field_data['class'] . '\');';
                }
                $lines[] = 'echo $' . $slugTag . ';';
                $lines[] = '?>';
                return implode(PHP_EOL, $lines);
            } else {
                $field_data['src'] = '<?php echo ' . $field_data['src'] . '; ?>';
                $field_data['alt'] = '<?php echo ' . $field_data['alt'] . '; ?>';
                $field_data = array_filter($field_data);
                $attributes = implode(' ', array_map(function ($v, $k) {
                    return sprintf('%s="%s"', $k, $v);
                }, $field_data, array_keys($field_data)));
                $img = '<img ' . $attributes . '/>';
                if (isset($this->data['thumbnail']) && $this->data['thumbnail'] == '1') {
                    $img = '<?php if (' . $imgThumb . ') {
                                ?>' . $img . '<?php
                            } ?>';
                }
                return $img;
            }
        }
    }

    public function getEditFunctionContents()
    {
        $return = null;
        if ($this->getRepeating()) {
            $return .= 'foreach ($' . $this->data['parent']['slug'] . '_items as &$' . $this->data['parent']['slug'] . '_item) {
            if (!File::getByID($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\'])) {
                unset($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\']);
            }
        }';
        }
        return $return;
    }

    private function generateLink($inner = '')
    {
        $html = null;
        $slugPage = $this->getRepeating() ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '_page"]' : $this->data['slug'] . '_page';
        $slugLink = $this->getRepeating() ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '_link"]' : $this->data['slug'] . '_link';
        $slugUrl = $this->getRepeating() ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '_url"]' : $this->data['slug'] . '_url';
        $newWindow = isset($this->data['url_target']) && is_string($this->data['url_target']) && $this->data['url_target'] == '1' ? true : false;
        switch ($this->data['link']) {
            case '1':
                $html = '<?php
        $' . $slugPage . ' = !empty($' . $slugLink . ') && (($page = Page::getByID($' . $slugLink . ')) && $page->error === false) ? $page : false;
        if ($' . $slugPage . ') {
            echo \'<a href="\' . $' . $slugPage . '->getCollectionLink() . \'"' . ($newWindow ? ' target="_blank"' : null) . (isset($this->data['link_class']) && is_string($this->data['link_class']) && trim($this->data['link_class']) != '' ? ' class="' . $this->data['link_class'] . '"' : null) . '>\';
        } ?>
        ' . $inner . '<?php
        if ($' . $slugPage . ') {
            echo \'</a>\';
        } ?>';
                break;
            case '2':
                $html = '<?php
        if (trim($' . $slugUrl . ') != "") {
            echo \'<a href="\' . $' . $slugUrl . ' . \'"' . ($newWindow ? ' target="_blank"' : null) . (isset($this->data['link_class']) && is_string($this->data['link_class']) && trim($this->data['link_class']) != '' ? ' class="' . $this->data['link_class'] . '"' : null) . '>\';
        } ?>
        ' . $inner . '<?php
        if (trim($' . $slugUrl . ') != "") {
            echo \'</a>\';
        } ?>';
                break;
        }
        return $html;
    }

    public function getViewContents()
    {
        $slug = $this->getRepeating() ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '"]' : $this->data['slug'];
        $field_data = [
	        'src'   => isset($this->data['thumbnail_handle']) && trim($this->data['thumbnail_handle']) != '' ? '$' . $slug . '->getThumbnailURL(\'' . $this->data['thumbnail_handle'] . '\')' : '$' . $slug . '->getURL()',
            'alt'   => '$' . $slug . '->getTitle()',
            'class' => isset($this->data['class']) && is_string($this->data['class']) && trim($this->data['class']) != '' ? htmlentities(preg_replace('!\s+!', ' ', $this->data['class'])) : null,
        ];
        $img = $this->generateImage($field_data);
        if (isset($this->data['link']) && in_array($this->data['link'], [1, 2])) {
            $img = $this->generateLink($img);
        }
        return '<?php if ($' . $slug . ') { ?>' . $this->data['prefix'] . $img . $this->data['suffix'] . '<?php } ?>';
    }

    public function getViewFunctionContents()
    {
        if ($this->getRepeating()) {
            return 'if (isset($' . $this->data['parent']['slug'] . '_item_v[\'' . $this->data['slug'] . '\']) && trim($' . $this->data['parent']['slug'] . '_item_v[\'' . $this->data['slug'] . '\']) != "" && ($f = File::getByID($' . $this->data['parent']['slug'] . '_item_v[\'' . $this->data['slug'] . '\'])) && is_object($f)) {
                $' . $this->data['parent']['slug'] . '_item_v[\'' . $this->data['slug'] . '\'] = $f;
            } else {
                $' . $this->data['parent']['slug'] . '_item_v[\'' . $this->data['slug'] . '\'] = false;
            }';
        } else {
            return '
        if ($this->' . $this->data['slug'] . ' && ($f = File::getByID($this->' . $this->data['slug'] . ')) && is_object($f)) {
            $this->set("' . $this->data['slug'] . '", $f);
        } else {
            $this->set("' . $this->data['slug'] . '", false);
        }';
        }
    }

    public function getRepeatableUpdateItemJS()
    {
        $slug = 'ftImage' . ucfirst($this->data['slug']);
        $slugPage = 'pageSelector' . ucfirst($this->data['slug']);
        return 'var ' . $slug . ' = $(newField).find(\'.ft-image-' . $this->data['slug'] . '-file-selector\');
            if ($(' . $slug . ').length > 0) {
                $(' . $slug . ').concreteFileSelector({\'inputName\': $(' . $slug . ').attr(\'data-file-selector-input-name\'), \'filters\': [], \'fID\' : $(' . $slug . ').attr(\'data-file-selector-f-id\') });
            }
            var ' . $slugPage . ' = $(newField).find(\'.ft-image-' . $this->data['slug'] . '-page-selector\');
            if ($(' . $slugPage . ').length > 0) {
                $(' . $slugPage . ').concretePageSelector({\'inputName\': $(' . $slugPage . ').attr(\'data-input-name\'), \'cID\' : $(' . $slugPage . ').attr(\'data-cID\')});
            }';
    }

    public function getValidateFunctionContents()
    {
        $repeating = $this->getRepeating();
        if ($repeating) {
            $slug = '$' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']';
            $slugUrl = '$' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '_url\']';
            $slugLink = '$' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '_link\']';
            $validation = 'if (in_array("' . $this->data['slug'] . '", $this->btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']) && (!isset(' . $slug . ') || trim(' . $slug . ') == "" || !is_object(File::getByID(' . $slug . ')))) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("' . h($this->data['label']) . '"), t("' . h($this->data['parent']['label']) . '"), $' . $this->data['parent']['slug'] . '_k));
                        }';
            if (isset($this->data['link']) && in_array($this->data['link'], [1, 2])) {
                switch ($this->data['link']) {
                    case '1':
                        $validation .= ' elseif (!isset(' . $slugLink . ') || trim(' . $slugLink . ') == "" || (is_object(File::getByID(' . $slug . ')) && (($page = Page::getByID(' . $slugLink . ')) && $page->error !== false))) {
                              $e->add(t("The %s link field is required (%s, row #%s).", t("' . h($this->data['label']) . '"), t("' . h($this->data['parent']['label']) . '"), $' . $this->data['parent']['slug'] . '_k));
                        }';
                        break;
                    case '2':
                        $validation .= 'elseif (is_object(File::getByID(' . $slug . ')) && (trim(' . $slugUrl . ') == "" || !filter_var(' . $slugUrl . ', FILTER_VALIDATE_URL))) {
                              $e->add(t("The %s URL field does not have a valid URL (%s, row #%s).", t("' . h($this->data['label']) . '"), t("' . h($this->data['parent']['label']) . '"), $' . $this->data['parent']['slug'] . '_k));
                        }';
                        break;
                }
            }
            return $validation;
        } else {
            $validation = 'if (in_array("' . $this->data['slug'] . '", $this->btFieldsRequired) && (trim($args["' . $this->data['slug'] . '"]) == "" || !is_object(File::getByID($args["' . $this->data['slug'] . '"])))) {
            $e->add(t("The %s field is required.", t("' . h($this->data['label']) . '")));
        }';
            if (isset($this->data['link']) && in_array($this->data['link'], [1, 2])) {
                switch ($this->data['link']) {
                    case '1':
                        $validation .= ' elseif (is_object(File::getByID($args["' . $this->data['slug'] . '"])) && (($page = Page::getByID($args["' . $this->data['slug'] . '_link"])) && $page->error !== false)) {
              $e->add(t("The %s link field is required.", t("' . h($this->data['label']) . '")));
        }';
                        break;
                    case '2':
                        $validation .= 'elseif (is_object(File::getByID($args["' . $this->data['slug'] . '"])) && (trim($args["' . $this->data['slug'] . '_url"]) == "" || !filter_var($args["' . $this->data['slug'] . '_url"], FILTER_VALIDATE_URL))) {
              $e->add(t("The %s URL field does not have a valid URL.", t("' . h($this->data['label']) . '")));
        }';
                        break;
                }
            }
            return $validation;
        }
    }

    public function getFormContents()
    {
        $html = '';
        $repeating = $this->getRepeating();
        $btFieldsRequired = $repeating ? '$btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']' : '$btFieldsRequired';
        if ($repeating) {
            $html .= '<div class="form-group">
    ' . parent::generateFormContent('label', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => $this->data['label'], 'description' => $this->data['description']], $repeating) . '
    ' . parent::generateFormContent('required', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'array' => $btFieldsRequired], $repeating) . '
    ' . parent::generateFormContent('image', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'attributes' => ['class' => 'ccm-file-selector ft-image-' . $this->data['slug'] . '-file-selector']], $repeating) . '
</div>';
        } else {
            $slug = $repeating ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '"]' : $this->data['slug'];
            $slugO = $repeating ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '_o"]' : $this->data['slug'] . '_o';
            $html .= '<div class="form-group">
    <?php
    if (isset($' . $slug . ') && $' . $slug . ' > 0) {
        $' . $slugO . ' = File::getByID($' . $this->data['slug'] . ');
        if (!is_object($' . $slugO . ')) {
            unset($' . $slugO . ');
        }
    } ?>
    ' . parent::generateFormContent('label', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => $this->data['label'], 'description' => $this->data['description']], $repeating) . '
    ' . parent::generateFormContent('required', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'array' => $btFieldsRequired], $repeating) . '
    ' . parent::generateFormContent('image', ['slug' => 'ccm-b-' . $this->data['block_handle'] . '-' . $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'postName' => $this->data['slug'], 'bf' => '$' . $this->data['slug'] . '_o'], $repeating) . '
</div>';
        }
        if (isset($this->data['link']) && in_array($this->data['link'], [1, 2])) {
            switch ($this->data['link']) {
                case '1':
                    $html .= PHP_EOL . '<div class="form-group">
    ' . parent::generateFormContent('label', ['slug' => $this->data['slug'] . '_link', 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => $this->data['label'], 'suffix' => ' . " " . t("link")'], $repeating) . '
    ' . parent::generateFormContent('page_selector', ['slug' => $this->data['slug'] . '_link', 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'attributes' => array('class' => 'ft-image-' . $this->data['slug'] . '-page-selector')], $repeating) . '
</div>';
                    break;
                case '2':
                    $html .= PHP_EOL . '<div class="form-group">
    ' . parent::generateFormContent('label', ['slug' => $this->data['slug'] . '_url', 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => $this->data['label'], 'suffix' => ' . " " . t("url")'], $repeating) . '
    ' . parent::generateFormContent('required', [], $repeating) . '
    ' . parent::generateFormContent('text', ['slug' => $this->data['slug'] . '_url', 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'attributes' => ['maxlength' => 255]], $repeating) . '
</div>';
                    break;
            }
        }
        return $html;
    }

    public function getSaveFunctionContents()
    {
        if ($this->getRepeating()) {
            $lines = [];
            $lines[] = 'if (isset($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\']) && trim($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\']) != \'\') {
                    $data[\'' . $this->data['slug'] . '\'] = trim($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\']);
                } else {
                    $data[\'' . $this->data['slug'] . '\'] = null;
                }';
            if (isset($this->data['link']) && in_array($this->data['link'], [1, 2])) {
                switch ($this->data['link']) {
                    case '1':
                        $lines[] = 'if (isset($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '_link\']) && trim($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '_link\']) != \'\') {
                    $data[\'' . $this->data['slug'] . '_link\'] = trim($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '_link\']);
                } else {
                    $data[\'' . $this->data['slug'] . '_link\'] = null;
                }';
                        break;
                    case '2':
                        $lines[] = 'if (isset($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '_url\']) && trim($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '_url\']) != \'\') {
                    $data[\'' . $this->data['slug'] . '_url\'] = trim($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '_url\']);
                } else {
                    $data[\'' . $this->data['slug'] . '_url\'] = null;
                }';
                        break;
                }
            }
            return implode(PHP_EOL, $lines);
        }
    }

    public function getFieldOptions()
    {
        return parent::view('field_options.php');
    }

    public function getDbFields()
    {
        $dbFields = [
            [
                'name'       => $this->data['slug'],
                'type'       => $this->getDbType(),
                'attributes' => [
                    'default' => '0',
                    'notnull' => true,
                ],
            ]
        ];
        if (isset($this->data['link']) && in_array($this->data['link'], [1, 2])) {
            switch ($this->data['link']) {
                case '1':
                    $dbFields[] = [
                        'name' => $this->data['slug'] . '_link',
                        'type' => 'I',
                    ];
                    break;
                case '2':
                    $dbFields[] = [
                        'name' => $this->data['slug'] . '_url',
                        'type' => 'C',
                    ];
                    break;
            }
        }
        return $dbFields;
    }

	public function getBtExportFileColumn()
	{
		return [$this->data['slug']];
	}

    public function getAssets()
    {
        return [
            'addEdit' => [
                'require'  => [
                    [
                        'handle' => 'core/file-manager',
                    ],
                ],
            ],
        ];
    }
}