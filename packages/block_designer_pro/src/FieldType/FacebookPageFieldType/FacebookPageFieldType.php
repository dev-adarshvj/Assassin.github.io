<?php namespace RamonLeenders\BlockDesignerPro\FieldType\FacebookPageFieldType;

defined('C5_EXECUTE') or die("Access Denied.");

use RamonLeenders\BlockDesigner\FieldType\FieldType;

class FacebookPageFieldType extends FieldType
{
    protected $ftHandle = 'facebook_page';
    protected $dbType = 'C';
    protected $canRepeat = true;
    protected $pkgVersionRequired = '1.2.4';

    public function getFieldName()
    {
        return t("Facebook Page");
    }

    public function getFieldDescription()
    {
        return t("A Facebook Page Plugin field");
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
        $attributesArray = [];
        $attributes = [
	        'href'          => '<?php echo $' . $slug . '; ?>',
	        'width'         => isset($this->data['width']) && $this->data['width'] >= 280 && $this->data['width'] <= 500 ? $this->data['width'] : 340,
	        'height'        => isset($this->data['height']) && $this->data['height'] >= 130 ? $this->data['height'] : 500,
	        'hide-cover'    => isset($this->data['hide_cover']) && $this->data['hide_cover'] == '1' ? 'true' : 'false',
	        'show-facepile' => isset($this->data['show_facepile']) && $this->data['show_facepile'] == '1' ? 'true' : 'false',
	        'show-posts'    => isset($this->data['show_posts']) && $this->data['show_posts'] == '1' ? 'true' : 'false',
        ];
        foreach ($attributes as $k => $v) {
            $attributesArray[] = 'data-' . $k . '="' . $v . '"';
        }
        return '<?php if (isset($' . $slug . ') && trim($' . $slug . ') != "") { ?>' . $this->data['prefix'] . '<?php if (strpos($' . $slug . ', "http") === false) {
        $' . $slug . ' = "http://" . $' . $slug . ';
    } ?><div class="fb-page" ' . implode(' ', $attributesArray) . '></div>' . $this->data['suffix'] . '<?php } ?>';
    }

    public function getValidateFunctionContents()
    {
        if ($this->getRepeating()) {
            return 'if (in_array("' . $this->data['slug'] . '", $this->btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']) && (!isset($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']) || trim($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']) == "")) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("' . h($this->data['label']) . '"), t("' . h($this->data['parent']['label']) . '"), $' . $this->data['parent']['slug'] . '_k));
                        } elseif (isset($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\']) && trim($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\']) != "") {
                            preg_match(\'~^(https?://)?(www\.)?facebook\.com/\w{5,}$~i\', $' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\'], $matches);
                            if (empty($matches)) {
                                $e->add(t("The %s field does not have a valid Facebook Page URL (%s, row #%s).", t("' . h($this->data['label']) . '"), t("' . h($this->data['parent']['label']) . '"), $' . $this->data['parent']['slug'] . '_k));
                            }
                        }';
        } else {
            return 'if (in_array("' . $this->data['slug'] . '", $this->btFieldsRequired) && trim($args["' . $this->data['slug'] . '"]) == "") {
            $e->add(t("The %s field is required.", t("' . h($this->data['label']) . '")));
        } elseif (trim($args["' . $this->data['slug'] . '"]) != "") {
            preg_match(\'~^(https?://)?(www\.)?facebook\.com/\w{5,}$~i\', $args["' . $this->data['slug'] . '"], $matches);
            if (empty($matches)) {
                $e->add(t("The %s field does not have a valid Facebook Page URL.", t("' . h($this->data['label']) . '")));
            }
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
        $fieldAttributes = ['maxlength' => 255];
        return '<div class="form-group">
    ' . parent::generateFormContent('label', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => $this->data['label'], 'description' => $this->data['description']], $repeating) . '
    ' . parent::generateFormContent('required', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'array' => $btFieldsRequired], $repeating) . '
    ' . parent::generateFormContent('text', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'attributes' => $fieldAttributes], $repeating) . '
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
				'size' => 255,
			]
		];
	}
}