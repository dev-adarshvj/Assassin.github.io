<?php namespace RamonLeenders\BlockDesigner\FieldType\YoutubeFieldType;

defined('C5_EXECUTE') or die("Access Denied.");

use RamonLeenders\BlockDesigner\FieldType\FieldType;

class YoutubeFieldType extends FieldType
{
    protected $ftHandle = 'youtube';
    protected $dbType = 'C';
    protected $canRepeat = true;

    public function getFieldName()
    {
        return t("YouTube");
    }

    public function getFieldDescription()
    {
        return t("A YouTube embed field");
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
        // https://developers.google.com/youtube/player_parameters
        $getData = [];
        $baseURL = isset($this->data['privacy']) && $this->data['privacy'] == '1' ? 'https://www.youtube-nocookie.com/embed/' : 'https://www.youtube.com/embed/';
        $size = [420, 315];
        if (isset($this->data['size']) && trim($this->data['size']) != '') {
            switch ($this->data['size']) {
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
        if (isset($this->data['related']) && $this->data['related'] == '0') {
            $getData['rel'] = '0';
        }
        if (isset($this->data['controls']) && $this->data['controls'] == '0') {
            $getData['controls'] = '0';
        }
        if (isset($this->data['showinfo']) && $this->data['showinfo'] == '0') {
            $getData['showinfo'] = '0';
        }
        if (isset($this->data['autohide']) && in_array($this->data['autohide'], [0, 1, 2])) {
            $getData['autohide'] = $this->data['autohide'];
        }
        if (isset($this->data['autoplay']) && $this->data['autoplay'] == '1') {
            $getData['autoplay'] = '1';
        }
        if (isset($this->data['start']) && (int)$this->data['start'] > 0) {
            $getData['start'] = (int)$this->data['start'];
        }
        if (isset($this->data['end']) && (int)$this->data['end'] > 0) {
            $getData['end'] = (int)$this->data['end'];
        }
        if (isset($this->data['cc_load_policy']) && $this->data['cc_load_policy'] == '1') {
            $getData['cc_load_policy'] = '1';
        }
        if (isset($this->data['disablekb']) && $this->data['disablekb'] == '1') {
            $getData['disablekb'] = '1';
        }
        if (isset($this->data['fs']) && $this->data['fs'] == '0') {
            $getData['fs'] = '0';
        }
        if (isset($this->data['iv_load_policy']) && $this->data['iv_load_policy'] == '3') {
            $getData['iv_load_policy'] = '3';
        }
        if (isset($this->data['loop']) && $this->data['loop'] == '1') {
            $getData['loop'] = '1';
        }
        if (isset($this->data['modestbranding']) && $this->data['modestbranding'] == '1') {
            $getData['modestbranding'] = '1';
        }
        $iframeData = [
            'width'           => isset($size[0]) ? $size[0] : 420,
            'height'          => isset($size[1]) ? $size[1] : 315,
            'src'             => $baseURL . '<?php echo $' . $slug . '; ?>' . (!empty($getData) ? '?' . h(http_build_query($getData)) . (isset($getData['loop']) ? '&amp;playlist=<?php echo $' . $slug . '; ?>' : null) : null),
            'frameborder'     => '0',
            'allowfullscreen' => null,
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
        ?><div style="width: ' . (is_numeric($size[0]) ? $size[0] . 'px' : $size[0]) . '; height: ' . (is_numeric($size[1]) ? $size[1] . 'px' : $size[1]) . '; display: table;" class="ccm-edit-mode-disabled-item">
            <span style="display: table-cell; vertical-align: middle;">
                <?php echo t(\'YouTube Video disabled in edit mode.\'); ?>
            </span>
        </div><?php
    } else {
        ?><iframe ' . $iframeAttributes . '></iframe><?php
    } ?>' . $this->data['suffix'] . '<?php } ?>';
    }

    public function getViewFunctionContents()
    {
        $repeating = $this->getRepeating();
        $slug = $repeating ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '"]' : $this->data['slug'];
        $slugCode = $repeating ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '_code"]' : $this->data['slug'] . '_code';
        if ($repeating) {
            return '$' . $slugCode . ' = null;
            preg_match(\'~^(?:https?://)?(?:www\.)?(?:youtube\.com|youtu\.be)/watch\?v=([^&]+)~x\', isset($' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"]) ? $' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"] : null, $matches);
            if (!empty($matches) && isset($matches[1]) && trim($matches[1]) != "") {
                $' . $slugCode . ' = $matches[1];
            }
            $' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"] = $' . $slugCode . ';';
        } else {
            return '$' . $slugCode . ' = null;
        preg_match(\'~^(?:https?://)?(?:www\.)?(?:youtube\.com|youtu\.be)/watch\?v=([^&]+)~x\', $this->' . $this->data['slug'] . ', $matches);
        if (!empty($matches) && isset($matches[1]) && trim($matches[1]) != "") {
            $' . $slugCode . ' = $matches[1];
        }
        $this->set("' . $slug . '", $' . $slugCode . ');';
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
                        } else {
                            if (isset($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']) && trim($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']) != "") {
                                preg_match(\'~^(?:https?://)?(?:www\.)?(?:youtube\.com|youtu\.be)/watch\?v=([^&]+)~x\', $' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\'], $matches);
                                if ((!isset($matches[1]) || trim($matches[1]) == "")) {
                                    $e->add(t("The %s field contains an invalid YouTube URL (%s, row #%s).", t("' . h($this->data['label']) . '"), t("' . h($this->data['parent']['label']) . '"), $' . $this->data['parent']['slug'] . '_k));
                                }
                            }
                        }';
        } else {
            return 'if (in_array("' . $this->data['slug'] . '", $this->btFieldsRequired) && trim($args["' . $this->data['slug'] . '"]) == "") {
            $e->add(t("The %s field is required.", t("' . h($this->data['label']) . '")));
        } else {
            preg_match(\'~^(?:https?://)?(?:www\.)?(?:youtube\.com|youtu\.be)/watch\?v=([^&]+)~x\', $args["' . $this->data['slug'] . '"], $matches);
            if ($args["' . $this->data['slug'] . '"] != "" && (!isset($matches[1]) || trim($matches[1]) == "")) {
                $e->add(t("The %s field contains an invalid YouTube URL.", t("' . h($this->data['label']) . '")));
            }
        }';
        }
    }

    public function getFormContents()
    {
        $repeating = $this->getRepeating();
        $btFieldsRequired = $repeating ? '$btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']' : '$btFieldsRequired';
        return '<div class="form-group">
    ' . parent::generateFormContent('label', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => $this->data['label'], 'description' => $this->data['description']], $repeating) . '
    ' . parent::generateFormContent('required',['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'array' => $btFieldsRequired], $repeating) . '
    ' . parent::generateFormContent('text', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null], $repeating) . '
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
                'size' => 255,
            ]
        ];
    }
}