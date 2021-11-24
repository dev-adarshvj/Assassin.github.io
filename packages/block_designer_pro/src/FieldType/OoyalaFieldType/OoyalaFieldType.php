<?php namespace RamonLeenders\BlockDesignerPro\FieldType\OoyalaFieldType;

defined('C5_EXECUTE') or die("Access Denied.");

use RamonLeenders\BlockDesigner\FieldType\FieldType;

class OoyalaFieldType extends FieldType
{
    protected $ftHandle = 'ooyala';
    protected $dbType = 'C';
    protected $canRepeat = true;

    public function getFieldName()
    {
        return t("Ooyala");
    }

    public function getFieldDescription()
    {
        return t("An Ooyala embed field");
    }

    public function getViewContents()
    {
        $slug = $this->getRepeating() ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '"]' : $this->data['slug'];
        $getData = [];
        $size = [420, 315];
        if (isset($this->data['size']) && trim($this->data['size']) != '') {
            switch ($this->data['size']) {
                case 'default':
                    $size = [420, 315];
                    break;
                case 'medium':
                    $size = [480, 360];
                    break;
                case 'large':
                    $size = [640, 480];
                    break;
                case 'hd720':
                    $size = [960, 720];
                    break;
                case 'custom':
                    $size = [];
                    if (isset($this->data['width']) && trim($this->data['width']) != '') {
                        $size[0] = trim($this->data['width']);
                    }
                    if (isset($this->data['height']) && trim($this->data['height']) != '') {
                        $size[1] = trim($this->data['height']);
                    }
                    break;
                default:
                    break;
            }
        }
        $getData['width'] = $size[0];
        $getData['height'] = $size[1];
        if (isset($this->data['autoplay']) && $this->data['autoplay'] == '1') {
            $getData['autoplay'] = true;
        }
        if (isset($this->data['start']) && (int)$this->data['start'] > 0) {
            $getData['initialTime'] = (int)$this->data['start'];
        }
        if (isset($this->data['loop']) && $this->data['loop'] == '1') {
            $getData['loop'] = true;
        }
        if (isset($this->data['enableChannels']) && $this->data['enableChannels'] == '1') {
            $getData['enableChannels'] = true;
        }
        if (isset($this->data['tvRatingsPosition']) && is_string($this->data['tvRatingsPosition']) && in_array($this->data['tvRatingsPosition'], ['top-left', 'top-right', 'bottom-left', 'bottom-right'])) {
            $getData['tvRatingsPosition'] = $this->data['tvRatingsPosition'];
        }
        $getData['prebuffering'] = isset($this->data['prebuffering']) && $this->data['prebuffering'] == '1' ? true : false;
        $getData['showInAdControlBar'] = isset($this->data['showInAdControlBar']) && $this->data['showInAdControlBar'] == '1' ? true : false;
        $getData['useFirstVideoFromPlaylist'] = isset($this->data['useFirstVideoFromPlaylist']) && $this->data['useFirstVideoFromPlaylist'] == '1' ? true : false;
        $getData['showAdMarquee'] = isset($this->data['showAdMarquee']) && $this->data['showAdMarquee'] == '1' ? true : false;
        if (isset($this->data['tvRatingsTimer']) && is_string($this->data['tvRatingsTimer']) && in_array($this->data['tvRatingsTimer'], ['always', 'never', 'custom'])) {
            switch ($this->data['tvRatingsTimer']) {
                case 'custom':
                    if (isset($this->data['tvRatingsTimerSeconds']) && is_numeric($this->data['tvRatingsTimerSeconds'])) {
                        $getData['tvRatingsTimer'] = $this->data['tvRatingsTimerSeconds'];
                    }
                    break;
                default:
                    $getData['tvRatingsTimer'] = $this->data['tvRatingsTimer'];
                    break;
            }
        }
        $elementID = $this->getRepeating() ? '<?php echo \'ooyala-\' . $bID . \'-\' . $' . $this->data['parent']['slug'] . '_item_key' . '; ?>' : '<?php echo \'ooyala-\' . $bID; ?>';
        return '<?php if (isset($' . $slug . ') && trim($' . $slug . ') != "") { ?>' . $this->data['prefix'] . '<?php $c = Page::getCurrentPage();
    if ($c->isEditMode()) {
        ?><div style="width: ' . (is_numeric($size[0]) ? $size[0] . 'px' : $size[0]) . '; height: ' . (is_numeric($size[1]) ? $size[1] . 'px' : $size[1]) . '; display: table;" class="ccm-edit-mode-disabled-item">
            <span style="display: table-cell; vertical-align: middle;">
                <?php echo t(\'Ooyala Video disabled in edit mode.\'); ?>
            </span>
        </div><?php
    }
    else {
        ?><div id="' . $elementID . '"' . (isset($this->data['class']) && is_string($this->data['class']) && trim($this->data['class']) != '' ? ' class="' . h($this->data['class']) . '"' : null) . '></div>
        <script type="text/javascript">
            OO.ready(function () {
                var playerConfiguration = ' . json_encode($getData) . ';
                OO.Player.create(\'' . $elementID . '\', \'<?php echo $' . $slug . '; ?>\', playerConfiguration);
});
</script><?php
    } ?>' . $this->data['suffix'] . '<?php } ?>';
    }

    public function getViewFunctionContents()
    {
        if ($repeating = $this->getRepeating()) {
            return '$' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"] = isset($' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"]) ? $' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"] : null;';
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
        $repeating = $this->getRepeating();
        $btFieldsRequired = $repeating ? '$btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']' : '$btFieldsRequired';
        return '<div class="form-group">
    ' . parent::generateFormContent('label', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => $this->data['label'], 'description' => $this->data['description']], $repeating) . '
    ' . parent::generateFormContent('required', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'array' => $btFieldsRequired], $repeating) . '
    ' . parent::generateFormContent('text', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null], $repeating) . '
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