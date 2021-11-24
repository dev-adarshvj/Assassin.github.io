<?php namespace RamonLeenders\BlockDesignerPro\FieldType\TwitterTimelineFieldType;

defined('C5_EXECUTE') or die("Access Denied.");

use RamonLeenders\BlockDesigner\FieldType\FieldType;

class TwitterTimelineFieldType extends FieldType
{
    protected $ftHandle = 'twitter_timeline';
    protected $dbType = 'C';
    protected $canRepeat = true;
    protected $pkgVersionRequired = '1.2.4';

    public function getFieldName()
    {
        return t("Twitter Timeline");
    }

    public function getFieldDescription()
    {
        return t("A Twitter Embedded Timeline field");
    }

    public function getViewContents()
    {
        $slug = $this->getRepeating() ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '"]' : $this->data['slug'];
        $chrome = [];
        if (isset($this->data['no_header']) && $this->data['no_header'] == '1') {
            $chrome[] = 'noheader';
        }
        if (isset($this->data['no_footer']) && $this->data['no_footer'] == '1') {
            $chrome[] = 'nofooter';
        }
        if (isset($this->data['no_borders']) && $this->data['no_borders'] == '1') {
            $chrome[] = 'noborders';
        }
        if (isset($this->data['no_scrollbar']) && $this->data['no_scrollbar'] == '1') {
            $chrome[] = 'noscrollbar';
        }
        if (isset($this->data['transparent']) && $this->data['transparent'] == '1') {
            $chrome[] = 'transparent';
        }
        $iframeData = [
	        'class'          => 'twitter-timeline',
	        'data-widget-id' => '<?php echo $' . $slug . '; ?>',
        ];
        if (isset($this->data['theme']) && in_array($this->data['theme'], ['dark'])) {
            $iframeData['data-theme'] = $this->data['theme'];
        }
        if (isset($this->data['width']) && trim($this->data['width']) != '') {
            $iframeData['width'] = $this->data['width'];
        }
        if (isset($this->data['height']) && trim($this->data['height']) != '') {
            $iframeData['height'] = $this->data['height'];
        }
        if (!empty($chrome)) {
            $iframeData['data-chrome'] = implode(' ', $chrome);
        }
        if (isset($this->data['tweet_limit']) && is_numeric($this->data['tweet_limit']) && $this->data['tweet_limit'] >= 1 && $this->data['tweet_limit'] <= 20) {
            $iframeData['data-tweet-limit'] = $this->data['tweet_limit'];
        }
        if (isset($this->data['related']) && trim($this->data['related']) != '') {
            $iframeData['data-related'] = $this->data['related'];
        }
        if (isset($this->data['politeness']) && in_array($this->data['politeness'], ['assertive'])) {
            $iframeData['data-aria-polite'] = $this->data['politeness'];
        }
        $iframeAttributes = implode(' ', array_map(function ($v, $k) {
            if (trim($v) == '' || $v == null) {
                return sprintf('%s', $k);
            } else {
                return sprintf('%s="%s"', $k, $v);
            }
        }, $iframeData, array_keys($iframeData)));
        if (!isset($iframeData['width'])) {
            $iframeData['width'] = '100%';
        }
        if (!isset($iframeData['height'])) {
            $iframeData['height'] = '600';
        }
        return '<?php if (isset($' . $slug . ') && trim($' . $slug . ') != "") { ?>' . $this->data['prefix'] . '<?php $c = Page::getCurrentPage();
    if ($c->isEditMode()) {
        ?><div style="width: ' . (is_numeric($iframeData['width']) ? $iframeData['width'] . 'px' : $iframeData['width']) . '; height: ' . (is_numeric($iframeData['height']) ? $iframeData['height'] . 'px' : $iframeData['height']) . '; display: table;" class="ccm-edit-mode-disabled-item">
            <span style="display: table-cell; vertical-align: middle;">
                <?php echo t(\'Twitter Embedded Timeline disabled in edit mode.\'); ?>
            </span>
        </div><?php
    }
    else {
        ?><a ' . $iframeAttributes . '>Tweets</a><?php
    } ?>' . $this->data['suffix'] . '<?php } ?>';
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
    <div class="alert alert-info"><?php echo t(\'Enter the <a href="%s" target="_blank">Twitter Widget</a> ID here\', \'https://twitter.com/settings/widgets\'); ?></div>
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
		    ],
	    ];
    }
}