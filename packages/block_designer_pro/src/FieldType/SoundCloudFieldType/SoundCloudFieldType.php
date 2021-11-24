<?php namespace RamonLeenders\BlockDesignerPro\FieldType\SoundCloudFieldType;

defined('C5_EXECUTE') or die("Access Denied.");

use RamonLeenders\BlockDesigner\FieldType\FieldType;
use Concrete\Core\File\Service\File;

class SoundCloudFieldType extends FieldType
{
    protected $ftHandle = 'sound_cloud';
    protected $dbType = 'C';
    protected $canRepeat = true;
    protected $pkgVersionRequired = '1.2.4';

    public function getFieldName()
    {
        return t('SoundCloud');
    }

    public function getFieldDescription()
    {
        return t("A Sound Cloud field");
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

    public function getViewFunctionContents()
    {
        $repeating = $this->getRepeating();
        $slug = $repeating ? $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"]' : $this->data['slug'];
        if ($repeating) {
            return '$' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"] = (isset($' . $slug . ') && ($soundCloudUrl = $this->soundCloudUrl($' . $slug . '))) ? $soundCloudUrl : null;';
        } else {
            return '$this->set("' . $this->data['slug'] . '", ($soundCloudUrl = $this->soundCloudUrl($this->' . $slug . ')) ? $soundCloudUrl : null);';
        }
    }

    public function getViewContents()
    {
        $slug = $this->getRepeating() ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '"]' : $this->data['slug'];
        $formData = [
	        'auto_play'     => isset($this->data['autoplay']) && $this->data['autoplay'] == '1' ? 'true' : 'false',
	        'hide_related'  => isset($this->data['hide_related']) && $this->data['hide_related'] == '1' ? 'true' : 'false',
	        'show_comments' => isset($this->data['show_comments']) && $this->data['show_comments'] == '1' ? 'true' : 'false',
	        'show_reposts'  => isset($this->data['show_reposts']) && $this->data['show_reposts'] == '1' ? 'true' : 'false',
	        'show_user'     => isset($this->data['show_user']) && $this->data['show_user'] == '1' ? 'true' : 'false',
	        'visual'        => isset($this->data['visual']) && $this->data['visual'] == '1' ? 'true' : 'false',
        ];
        $iframeData = [
	        'src'                   => 'https://w.soundcloud.com/player/?url=<?php echo $' . $slug . '; ?>&' . http_build_query($formData),
	        'width'                 => isset($this->data['width']) && (is_numeric($this->data['width']) || trim($this->data['width']) != '') ? $this->data['width'] : '100%',
	        'height'                => isset($this->data['height']) && (is_numeric($this->data['height']) || trim($this->data['height']) != '') ? $this->data['height'] : 450,
	        'frameborder'           => 0,
	        'webkitallowfullscreen' => null,
	        'mozallowfullscreen'    => null,
	        'allowfullscreen'       => null,
        ];
        if (isset($this->data['class']) && is_string($this->data['class']) && trim($this->data['class']) != '') {
            $iframeData['class'] = $this->data['class'];
        }
        $iframeAttributes = implode(' ', array_map(function ($v, $k) {
            if (trim($v) == '' || $v == null) {
                return sprintf('%s', $k);
            } else {
                return sprintf('%s="%s"', $k, $v);
            }
        }, $iframeData, array_keys($iframeData)));
        return '<?php if (isset($' . $slug . ') && trim($' . $slug . ') != "") { ?>' . $this->data['prefix'] . '<?php $c = Page::getCurrentPage();
    if ($c->isEditMode()) {
        ?><div style="width: ' . (is_numeric($iframeData['width']) ? $iframeData['width'] . 'px' : $iframeData['width']) . '; height: ' . (is_numeric($iframeData['height']) ? $iframeData['height'] . 'px' : $iframeData['height']) . '; display: table;" class="ccm-edit-mode-disabled-item">
            <span style="display: table-cell; vertical-align: middle;">
                <?php echo t(\'SoundCloud disabled in edit mode.\'); ?>
            </span>
        </div><?php
    }
    else {
        ?><iframe ' . $iframeAttributes . '></iframe><?php
    } ?>' . $this->data['suffix'] . '<?php } ?>';
    }

    public function getValidateFunctionContents()
    {
        if ($this->getRepeating()) {
            return 'if (in_array("' . $this->data['slug'] . '", $this->btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']) && (!isset($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']) || trim($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']) == "")) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("' . h($this->data['label']) . '"), t("' . h($this->data['parent']['label']) . '"), $' . $this->data['parent']['slug'] . '_k));
                        } else {
                            if (isset($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']) && trim($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']) != "") {
                                if (!$this->soundCloudUrl($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\'])) {
                                    $e->add(t("The %s field does not have a valid SoundCloud URL (%s, row #%s).", t("' . h($this->data['label']) . '"), t("' . h($this->data['parent']['label']) . '"), $' . $this->data['parent']['slug'] . '_k));
                                }
                            }
                        }';
        } else {
            return 'if (in_array("' . $this->data['slug'] . '", $this->btFieldsRequired) && trim($args["' . $this->data['slug'] . '"]) == "") {
            $e->add(t("The %s field is required.", t("' . h($this->data['label']) . '")));
        } elseif (trim($args["' . $this->data['slug'] . '"]) != "") {
            if (!$this->soundCloudUrl($args["' . $this->data['slug'] . '"])) {
                $e->add(t("The %s field does not have a valid SoundCloud URL.", t("' . h($this->data['label']) . '")));
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

    public function getExtraFunctionsContents()
    {
        if ($this->data['ft_count'] > 0) {
            return;
        }
        $fileService = new File();
        return $fileService->getContents($this->ftDirectory . 'elements' . DIRECTORY_SEPARATOR . 'extra_functions.txt');
    }

    public function getFormContents()
    {
        $repeating = $this->getRepeating();
        $btFieldsRequired = $repeating ? '$btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']' : '$btFieldsRequired';
        return '<div class="form-group">
    ' . parent::generateFormContent('label', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => $this->data['label'], 'description' => $this->data['description']], $repeating) . '
    ' . parent::generateFormContent('required', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'array' => $btFieldsRequired], $repeating) . '
    ' . parent::generateFormContent('text', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'attributes' => ['maxlength' => 255]], $repeating) . '
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