<?php namespace RamonLeenders\BlockDesigner\FieldType\FileFieldType;

defined('C5_EXECUTE') or die("Access Denied.");

use RamonLeenders\BlockDesigner\FieldType\FieldType;

class FileFieldType extends FieldType
{
	protected $ftHandle = 'file';
	protected $dbType = 'I';
	protected $uses = ['File', 'Page', 'Permissions', 'URL'];
	protected $canRepeat = true;

	public function getFieldDescription()
	{
		return t("A file field");
	}

	public function getViewContents()
	{
		$repeating = $this->getRepeating();
		$slug = $repeating ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '"]' : $this->data['slug'];
		$slugTitle = $repeating ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '_title"]' : $this->data['slug'] . '_title';
		$href = '<?php echo $' . $slug . '->urls["relative"]; ?>';
		if (isset($this->data['download']) && $this->data['download'] == '1') {
			$href = '<?php echo isset($' . $slug . '->urls["download"]) ? $' . $slug . '->urls["download"] : $' . $slug . '->urls["relative"]; ?>';
		}
		$newWindow = isset($this->data['url_target']) && is_string($this->data['url_target']) && $this->data['url_target'] == '1' ? true : false;
		return '<?php if (isset($' . $slug . ') && $' . $slug . ' !== false) { ?>' . $this->data['prefix'] . '<a href="' . $href . '"' . ($newWindow ? ' target="_blank"' : null) . (isset($this->data['link_class']) && is_string($this->data['link_class']) && trim($this->data['link_class']) != '' ? ' class="' . $this->data['link_class'] . '"' : null) . '>
            <?php echo isset($' . $slugTitle . ') && trim($' . $slugTitle . ') != "" ? h($' . $slugTitle . ') : $' . $slug . '->getTitle(); ?>
        </a>' . $this->data['suffix'] . '<?php } ?>';
	}

	public function getViewFunctionContents()
	{
		$repeating = $this->getRepeating();
		$slug = $repeating ? $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"]' : $this->data['slug'];
		$slugID = $repeating ? $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '_id"]' : $this->data['slug'] . '_id';
		$slugFile = $repeating ? $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '_file"]' : $this->data['slug'] . '_file';
		$slugTitle = $repeating ? $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '_title"]' : $this->data['slug'] . '_title';
		if ($repeating) {
			$code = '$' . $slugID . ' = isset($' . $slug . ') && trim($' . $slug . ') != "" ? (int)$' . $slug . ' : false;
            $' . $slug . ' = false;
            if ($' . $slugID . ' > 0 && ($' . $slugFile . ' = File::getByID($' . $slugID . ')) && is_object($' . $slugFile . ')) {
                $fp = new Permissions($' . $slugFile . ');
                if ($fp->canViewFile()) {
                    $urls = [\'relative\' => $' . $slugFile . '->getRelativePath()];
                    if (($c = Page::getCurrentPage()) && $c instanceof Page) {
                        $urls[\'download\'] = URL::to(\'/download_file\', $' . $slugID . ', $c->getCollectionID());
                    }
                    $' . $slugFile . '->urls = $urls;
                    $' . $slug . ' = $' . $slugFile . ';
                }
            }';
			if (isset($this->data['title_field']) && $this->data['title_field'] == '1' && (!isset($this->data['title_field_required']) || $this->data['title_field_required'] != '1') && isset($this->data['title_field_fallback_value']) && trim($this->data['title_field_fallback_value']) != '') {
				$code .= '
        if (!isset($' . $slugTitle . ') || trim($' . $slugTitle . ') == "") {
            $' . $slugTitle . '" = \'' . $this->data['title_field_fallback_value'] . '\';
        }';
			}
			return $code;
		} else {
			$code = '$' . $slugID . ' = (int)$this->' . $slug . ';
        $this->' . $slug . ' = false;
        if ($' . $slugID . ' > 0 && ($' . $slugFile . ' = File::getByID($' . $slugID . ')) && is_object($' . $slugFile . ')) {
            $fp = new Permissions($' . $slugFile . ');
	        if ($fp->canViewFile()) {
	            $urls = [\'relative\' => $' . $slugFile . '->getRelativePath()];
		        if (($c = Page::getCurrentPage()) && $c instanceof Page) {
			        $urls[\'download\'] = URL::to(\'/download_file\', $' . $slugID . ', $c->getCollectionID());
		        }
		        $' . $slugFile . '->urls = $urls;
		        $this->' . $slug . ' = $' . $slugFile . ';
            }
        }
        $this->set("' . $slug . '", $this->' . $slug . ');';
			if (isset($this->data['title_field']) && $this->data['title_field'] == '1' && (!isset($this->data['title_field_required']) || $this->data['title_field_required'] != '1') && isset($this->data['title_field_fallback_value']) && trim($this->data['title_field_fallback_value']) != '') {
				$code .= '
        if (!isset($this->' . $slugTitle . ') || trim($this->' . $slugTitle . ') == "") {
            $this->set("' . $slugTitle . '", \'' . $this->data['title_field_fallback_value'] . '\');
        }';
			}
			return $code;
		}
	}

	public function getValidateFunctionContents()
	{
		$repeating = $this->getRepeating();
		$btFieldsRequired = $repeating ? '$this->btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']' : '$this->btFieldsRequired';
		$slug = $repeating ? '$' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']' : '$args["' . $this->data['slug'] . '"]';
		$slugTitle = $repeating ? '$' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '_title\']' : '$args["' . $this->data['slug'] . '_title"]';
		if ($repeating) {
			$validation = 'if (in_array("' . $this->data['slug'] . '", ' . $btFieldsRequired . ') && (!isset(' . $slug . ') || trim(' . $slug . ') == "" || !is_object(File::getByID(' . $slug . ')))) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("' . h($this->data['label']) . '"), t("' . h($this->data['parent']['label']) . '"), $' . $this->data['parent']['slug'] . '_k));
                        }';
			if (isset($this->data['title_field']) && $this->data['title_field'] == '1' && isset($this->data['title_field_required']) && $this->data['title_field_required'] == '1') {
				$validation .= '
            if (!isset(' . $slugTitle . ') || trim(' . $slugTitle . ') == "") {
                $e->add(t("The %s title field is required (%s, row #%s).", t("' . h($this->data['label']) . '"), t("' . h($this->data['parent']['label']) . '"), $' . $this->data['parent']['slug'] . '_k));
            }';
			}
			return $validation;
		} else {
			$validation = 'if (in_array("' . $this->data['slug'] . '", ' . $btFieldsRequired . ') && (!isset(' . $slug . ') || trim(' . $slug . ') == "" || !is_object(File::getByID(' . $slug . ')))) {
            $e->add(t("The %s field is required.", t("' . h($this->data['label']) . '")));
        }';
			if (isset($this->data['title_field']) && $this->data['title_field'] == '1' && isset($this->data['title_field_required']) && $this->data['title_field_required'] == '1') {
				$validation .= '
            if (!isset(' . $slugTitle . ') || trim(' . $slugTitle . ') == "") {
            $e->add(t("The %s title field is required.", t("' . h($this->data['label']) . '")));
        }';
			}
			return $validation;
		}
	}

	public function getFormContents()
	{
		$repeating = $this->getRepeating();
		$btFieldsRequired = $repeating ? '$btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']' : '$btFieldsRequired';
		$html = null;
		if (!$repeating) {
			$html .= '<?php $' . $this->data['slug'] . '_o = null;
if ($' . $this->data['slug'] . ' > 0) {
    $' . $this->data['slug'] . '_o = File::getByID($' . $this->data['slug'] . ');
} ?>';
		}
		$slugFile = $repeating ? $this->data['slug'] : 'ccm-b-file-' . $this->data['slug'];
		$attributes = [];
		if ($repeating) {
			$attributes['class'] = 'ccm-file-selector ft-file-' . $this->data['slug'] . '-file-selector';
		}
		$html .= '
<div class="form-group">
    ' . parent::generateFormContent('label', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => $this->data['label'], 'description' => $this->data['description']], $repeating) . '
    ' . parent::generateFormContent('required', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'array' => $btFieldsRequired], $repeating) . '
    ' . parent::generateFormContent('file', ['slug' => $slugFile, 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'attributes' => $attributes, 'postName' => $this->data['slug'], 'bf' => '$' . $this->data['slug'] . '_o'], $repeating) . '
</div>';
		if (isset($this->data['title_field']) && $this->data['title_field'] == '1') {
			$html .= '
<div class="form-group">
    ' . parent::generateFormContent('label', ['slug' => $this->data['slug'] . '_title', 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => $this->data['label'], 'suffix' => ' . " " . t("Title")'], $repeating) . '
    ' . parent::generateFormContent('text', ['slug' => $this->data['slug'] . '_title', 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'attributes' => ['maxlength' => 255, 'placeholder' => isset($this->data['title_field_placeholder']) && trim($this->data['title_field_placeholder']) != '' ? h($this->data['title_field_placeholder']) : null]], $repeating) . '
</div>';
		}
		return $html;
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

	public function getSaveFunctionContents()
	{
		if ($this->getRepeating()) {
			$lines = ['if (isset($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\']) && trim($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\']) != \'\') {
                    $data[\'' . $this->data['slug'] . '\'] = trim($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\']);
                } else {
                    $data[\'' . $this->data['slug'] . '\'] = null;
                }'];
			if (isset($this->data['title_field']) && $this->data['title_field'] == '1') {
				$lines[] = 'if (isset($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '_title\']) && trim($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '_title\']) != \'\') {
                    $data[\'' . $this->data['slug'] . '_title\'] = trim($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '_title\']);
                } else {
                    $data[\'' . $this->data['slug'] . '_title\'] = null;
                }';
			}
			return implode(PHP_EOL, $lines);
		}
	}

	public function getRepeatableUpdateItemJS()
	{
		$slug = 'ftFile' . ucFirst($this->data['slug']);
		return 'var ' . $slug . ' = $(newField).find(\'.ft-file-' . $this->data['slug'] . '-file-selector\');
        if ($(' . $slug . ').length > 0) {
            var ' . $slug . 'ID = $(' . $slug . ').attr(\'data-file-selector-f-id\');
            $(' . $slug . ').concreteFileSelector({
                inputName: $(' . $slug . ').attr(\'data-file-selector-input-name\'),
                fID : ' . $slug . 'ID != \'0\' ? ' . $slug . 'ID : \'\'
            });
        }';
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
		if (isset($this->data['title_field']) && $this->data['title_field'] == '1') {
			$dbFields[] = [
				'name' => $this->data['slug'] . '_title',
				'type' => 'C',
			];
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
				'require' => [
					[
						'handle' => 'core/file-manager',
					],
				],
			],
		];
	}
}