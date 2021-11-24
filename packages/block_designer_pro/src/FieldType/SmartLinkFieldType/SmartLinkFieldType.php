<?php namespace RamonLeenders\BlockDesignerPro\FieldType\SmartLinkFieldType;

defined('C5_EXECUTE') or die("Access Denied.");

use RamonLeenders\BlockDesigner\BlockDesignerProcessor;
use RamonLeenders\BlockDesigner\FieldType\FieldType;
use Concrete\Core\File\Service\File;
use Zend\Code\Generator\ValueGenerator;
use Config;

class SmartLinkFieldType extends FieldType
{
	protected $ftHandle = 'smart_link';
	protected $pkgVersionRequired = '2.7.1';
	protected $dbType = 'C';
	protected $canRepeat = true;

	protected function varExportArray($array, $arrayStrip = false){
		$valueGenerator = version_compare(Config::get('concrete.version'), '8.0.0', '>=');
		$export = $valueGenerator ? (new ValueGenerator($array, ValueGenerator::TYPE_ARRAY_SHORT))->setIndentation('  ')->generate() : var_export($array, true);
		if ($arrayStrip) {
			$arrayEndString = $valueGenerator ? '\]' : '\)';
			$arrayEndStringReplace = $valueGenerator ? ']' : ')';
			$patterns = ['(\d+\s=>)', "/\s+/", "/\s([?.!])/", '/,'.$arrayEndString.'/', '/\',\'/', '/=>/', '/'.$arrayEndString.',\'/'];
			$replacer = ['', '', '$1', $arrayEndStringReplace, "', '", ' => ', $arrayEndStringReplace . ', \''];
			$export = preg_replace($patterns, $replacer, $export);
		}
		return $export;
	}

	public function getFieldName()
	{
		return t("Smart Link");
	}

	public function getFieldDescription()
	{
		return t("A smart link field");
	}

	public function getViewFunctionContents()
	{
		$repeating = $this->getRepeating();
		$options = $this->_options();
		$cases = [];
		$slug = $repeating ? $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"]' : $this->data['slug'];
		$slugTitle = $repeating ? $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '_Title"]' : $this->data['slug'] . '_Title';
		$slugFile = $repeating ? $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '_File"]' : $this->data['slug'] . '_File';
		$slugFileID = $repeating ? $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '_File_id"]' : $this->data['slug'] . '_File_id';
		$slugFileObject = $repeating ? $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '_File_object"]' : $this->data['slug'] . '_File_object';
		$slugPage = $repeating ? $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '_Page"]' : $this->data['slug'] . '_Page';
		$slugPageC = $repeating ? $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '_Page_c"]' : $this->data['slug'] . '_Page_c';
		$slugObject = $repeating ? $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '_Object"]' : $this->data['slug'] . '_Object';
		$slugImage = $repeating ? $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '_Image"]' : $this->data['slug'] . '_Image';
		$slugImageObject = $repeating ? $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '_Image_object"]' : $this->data['slug'] . '_Image_object';
		$slugURL = $repeating ? $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '_URL"]' : $this->data['slug'] . '_URL';
		$slugRelativeURL = $repeating ? $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '_Relative_URL"]' : $this->data['slug'] . '_Relative_URL';
		if (!$repeating) {
			if (in_array('page', $options)) {
				$cases[] = 'case \'page\':
					if ($this->' . $slugPage . ' > 0 && ($' . $slugPageC . ' = Page::getByID($this->' . $slugPage . ')) && !$' . $slugPageC . '->error && !$' . $slugPageC . '->isInTrash()) {
						$' . $slugObject . ' = $' . $slugPageC . ';
						$' . $slugURL . ' = $' . $slugPageC . '->getCollectionLink();
						if ($' . $slugTitle . ' == \'\') {
							$' . $slugTitle . ' = $' . $slugPageC . '->getCollectionName();
						}
					}
					break;';
			}
			if (in_array('file', $options)) {
				$fileDownloadLink = isset($this->data['file_download']) && $this->data['file_download'] == '1' ? PHP_EOL . '							if (($c = Page::getCurrentPage()) && $c instanceof Page) {
		                        $' . $slugURL . ' = URL::to(\'/download_file\', $' . $slugFileID . ', $c->getCollectionID());
		                    }' : null;
				$cases[] = 'case \'file\':
					$' . $slugFileID . ' = (int)$this->' . $slugFile . ';
					if ($' . $slugFileID . ' > 0 && ($' . $slugFileObject . ' = File::getByID($' . $slugFileID . ')) && is_object($' . $slugFileObject . ')) {
						$fp = new Permissions($' . $slugFileObject . ');
						if ($fp->canViewFile()) {
							$' . $slugObject . ' = $' . $slugFileObject . ';
							$' . $slugURL . ' = $' . $slugFileObject . '->getRelativePath();' . $fileDownloadLink . '
							if ($' . $slugTitle . ' == \'\') {
								$' . $slugTitle . ' = $' . $slugFileObject . '->getTitle();
							}
						}
					}
					break;';
			}
			if (in_array('url', $options)) {
				$cases[] = 'case \'url\':
					$' . $slugURL . ' = $this->' . $slugURL . ';
					if ($' . $slugTitle . ' == \'\') {
						$' . $slugTitle . ' = $' . $slugURL . ';
					}
					break;';
			}
			if (in_array('relative_url', $options)) {
				$cases[] = 'case \'relative_url\':
					$' . $slugURL . ' = $this->' . $slugRelativeURL . ';
					if ($' . $slugTitle . ' == \'\') {
						$' . $slugTitle . ' = $this->' . $slugRelativeURL . ';
					}
					break;';
			}
			if (in_array('image', $options)) {
				$cases[] = 'case \'image\':
					if ($this->' . $slugImage . ' && ($' . $slugImageObject . ' = File::getByID($this->' . $slugImage . ')) && is_object($' . $slugImageObject . ')) {
						$' . $slugURL . ' = $' . $slugImageObject . '->getURL();
						$' . $slugObject . ' = $' . $slugImageObject . ';
						if ($' . $slugTitle . ' == \'\') {
							$' . $slugTitle . ' = $' . $slugImageObject . '->getTitle();
						}
					}
					break;';
			}
			return '$' . $slugURL . ' = null;
		$' . $slugObject . ' = null;
		$' . $slugTitle . ' = trim($this->' . $slugTitle . ');
		if (trim($this->' . $slug . ') != \'\') {
			switch ($this->' . $slug . ') {
				' . implode(PHP_EOL . '				', $cases) . '
			}
		}
		$this->set("' . $slugURL . '", $' . $slugURL . ');
		$this->set("' . $slugObject . '", $' . $slugObject . ');
		$this->set("' . $slugTitle . '", $' . $slugTitle . ');';
		} else {
			if (in_array('page', $options)) {
				$cases[] = 'case \'page\':
						if ($' . $slugPage . ' > 0 && ($' . $slugPageC . ' = Page::getByID($' . $slugPage . ')) && !$' . $slugPageC . '->error && !$' . $slugPageC . '->isInTrash()) {
							$' . $slugObject . ' = $' . $slugPageC . ';
							$' . $slugURL . ' = $' . $slugPageC . '->getCollectionLink();
							if ($' . $slugTitle . ' == \'\') {
								$' . $slugTitle . ' = $' . $slugPageC . '->getCollectionName();
							}
						}
						break;';
			}
			if (in_array('file', $options)) {
				$fileDownloadLink = isset($this->data['file_download']) && $this->data['file_download'] == '1' ? PHP_EOL . '							    if (($c = Page::getCurrentPage()) && $c instanceof Page) {
			                        $' . $slugURL . ' = URL::to(\'/download_file\', $' . $slugFileID . ', $c->getCollectionID());
			                    }' : null;
				$cases[] = 'case \'file\':
						$' . $slugFileID . ' = (int)$' . $slugFile . ';
						if ($' . $slugFileID . ' > 0 && ($' . $slugFileObject . ' = File::getByID($' . $slugFileID . ')) && is_object($' . $slugFileObject . ')) {
							$fp = new Permissions($' . $slugFileObject . ');
							if ($fp->canViewFile()) {
								$' . $slugObject . ' = $' . $slugFileObject . ';
								$' . $slugURL . ' = $' . $slugFileObject . '->getRelativePath();' . $fileDownloadLink . '
								if ($' . $slugTitle . ' == \'\') {
									$' . $slugTitle . ' = $' . $slugFileObject . '->getTitle();
								}
							}
						}
						break;';
			}
			if (in_array('url', $options)) {
				$cases[] = 'case \'url\':
						if ($' . $slugTitle . ' == \'\') {
							$' . $slugTitle . ' = $' . $slugURL . ';
						}
						break;';
			}
			if (in_array('relative_url', $options)) {
				$cases[] = 'case \'relative_url\':
						if ($' . $slugTitle . ' == \'\') {
							$' . $slugTitle . ' = $' . $slugRelativeURL . ';
						}
						$' . $slugURL . ' = $' . $slugRelativeURL . ';
						break;';
			}
			if (in_array('image', $options)) {
				$cases[] = 'case \'image\':
						if ($' . $slugImage . ' > 0 && ($' . $slugImageObject . ' = File::getByID($' . $slugImage . ')) && is_object($' . $slugImageObject . ')) {
							$' . $slugURL . ' = $' . $slugImageObject . '->getURL();
							$' . $slugObject . ' = $' . $slugImageObject . ';
							if ($' . $slugTitle . ' == \'\') {
								$' . $slugTitle . ' = $' . $slugImageObject . '->getTitle();
							}
						}
						break;';
			}
			return '$' . $slugObject . ' = null;
			$' . $slugTitle . ' = trim($' . $slugTitle . ');
			if (isset($' . $slug . ') && trim($' . $slug . ') != \'\') {
				switch ($' . $slug . ') {
					' . implode(PHP_EOL . '				    ', $cases) . '
				}
			}';
		}
	}

	public function getUses()
	{
		$options = $this->_options();
		$uses = [];
		if (in_array('file', $options)) {
			if (isset($this->data['file_download']) && $this->data['file_download'] == '1') {
				$uses[] = 'URL';
			}
			$uses[] = 'Permissions';
		}
		if (in_array('file', $options) || in_array('image', $options)) {
			$uses[] = 'File';
		}
		if (in_array('file', $options) || in_array('page', $options)) {
			$uses[] = 'Page';
		}
		return $uses;
	}

	public function getViewContents()
	{
		$repeating = $this->getRepeating();
		$slug = $repeating ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '"]' : $this->data['slug'];
		$slugURL = $repeating ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '_URL"]' : $this->data['slug'] . '_URL';
		$slugTitle = $repeating ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '_Title"]' : $this->data['slug'] . '_Title';
		$slugAttributes = $repeating ? $this->data['parent']['slug'] . '_item' . $this->data['slug'] . '_Attributes' : $this->data['slug'] . '_Attributes';
		$slugAttributesHtml = $repeating ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '_AttributesHtml"]' : $this->data['slug'] . '_AttributesHtml';
		$targetBlanks = [];
		$options = $this->_options();
		foreach ($options as $v) {
			if (isset($this->data[$v . '_target_blank']) && $this->data[$v . '_target_blank'] == '1') {
				$targetBlanks[] = $v;
			}
		}
		$targetBlanksHtml = '';
		if (!empty($targetBlanks)) {
			$targetBlanksHtml = PHP_EOL . '	if (in_array($' . $slug . ', ' . $this->varExportArray($targetBlanks, true) . ')) {
		$' . $slugAttributes . '[\'target\'] = \'_blank\';
	}';
		}
		return '<?php
if (trim($' . $slugURL . ') != "") { ?>' . $this->data['prefix'] . '<?php
	$' . $slugAttributes . ' = [];
	$' . $slugAttributes . '[\'href\'] = $' . $slugURL . ';' . (isset($this->data['class']) && is_string($this->data['class']) && trim($this->data['class']) != '' ? PHP_EOL . '	$' . $slugAttributes . '[\'class\'] = \'' . h($this->data['class']) . '\';' : '') . $targetBlanksHtml . '
	$' . $slugAttributesHtml . ' = join(\' \', array_map(function ($key) use ($' . $slugAttributes . ') {
		return $key . \'="\' . $' . $slugAttributes . '[$key] . \'"\';
	}, array_keys($' . $slugAttributes . ')));
	echo sprintf(\'<a %s>%s</a>\', $' . $slugAttributesHtml . ', $' . $slugTitle . '); ?>' . $this->data['suffix'] . '<?php
} ?>';
	}

	public function getValidateFunctionContents()
	{
		$repeating = $this->getRepeating();
		$btFieldsRequired = $repeating ? '$this->btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']' : '$this->btFieldsRequired';
		$options = $this->_options();
		$label = 't("' . h($this->data['label']) . '")';
		$slug = $repeating ? '$' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']' : '$args["' . $this->data['slug'] . '"]';
		$slugPage = $repeating ? '$' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '_Page\']' : '$args["' . $this->data['slug'] . '_Page"]';
		$slugFile = $repeating ? '$' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '_File\']' : '$args["' . $this->data['slug'] . '_File"]';
		$slugURL = $repeating ? '$' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '_URL\']' : '$args["' . $this->data['slug'] . '_URL"]';
		$slugRelativeURL = $repeating ? '$' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '_Relative_URL\']' : '$args["' . $this->data['slug'] . '_Relative_URL"]';
		$slugImage = $repeating ? '$' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '_Image\']' : '$args["' . $this->data['slug'] . '_Image"]';
		$cases = [];
		if ($repeating) {
			if (in_array('page', $options)) {
				$cases[] = 'case \'page\':
									if (!isset(' . $slugPage . ') || trim(' . $slugPage . ') == "" || ' . $slugPage . ' == "0" || (($page = Page::getByID(' . $slugPage . ')) && $page->error !== false)) {
										$e->add(t("The %s field for \'%s\' is required (%s, row #%s).", t("Page"), ' . $label . ', t("' . h($this->data['parent']['label']) . '"), $' . $this->data['parent']['slug'] . '_k));
									}
									break;';
			}
			if (in_array('file', $options)) {
				$cases[] = 'case \'file\':
									if (!isset(' . $slugFile . ') || trim(' . $slugFile . ') == "" || !is_object(File::getByID(' . $slugFile . '))) {
										$e->add(t("The %s field for \'%s\' is required (%s, row #%s).", t("File"), ' . $label . ', t("' . h($this->data['parent']['label']) . '"), $' . $this->data['parent']['slug'] . '_k));
									}
									break;';
			}
			if (in_array('url', $options)) {
				$cases[] = 'case \'url\':
									if (!isset(' . $slugURL . ') || trim(' . $slugURL . ') == "" || !filter_var(' . $slugURL . ', FILTER_VALIDATE_URL)) {
										$e->add(t("The %s field for \'%s\' does not have a valid URL (%s, row #%s).", t("URL"), ' . $label . ', t("' . h($this->data['parent']['label']) . '"), $' . $this->data['parent']['slug'] . '_k));
									}
									break;';
			}
			if (in_array('relative_url', $options)) {
				$cases[] = 'case \'relative_url\':
									if (!isset(' . $slugRelativeURL . ') || trim(' . $slugRelativeURL . ') == "") {
										$e->add(t("The %s field for \'%s\' is required (%s, row #%s).", t("Relative URL"), ' . $label . ', t("' . h($this->data['parent']['label']) . '"), $' . $this->data['parent']['slug'] . '_k));
									}
									break;';
			}
			if (in_array('image', $options)) {
				$cases[] = 'case \'image\':
									if (!isset(' . $slugImage . ') || trim(' . $slugImage . ') == "" || !is_object(File::getByID(' . $slugImage . '))) {
										$e->add(t("The %s field for \'%s\' is required (%s, row #%s).", t("Image"), ' . $label . ', t("' . h($this->data['parent']['label']) . '"), $' . $this->data['parent']['slug'] . '_k));
									}
									break;';
			}
			return 'if ((in_array("' . $this->data['slug'] . '", ' . $btFieldsRequired . ') && (!isset(' . $slug . ') || trim(' . $slug . ') == "")) || (isset(' . $slug . ') && trim(' . $slug . ') != "" && !array_key_exists(' . $slug . ', $this->getSmartLinkTypeOptions(' . $this->varExportArray($this->_options(), true) . ')))) {
							$e->add(t("The %s field has an invalid value.", ' . $label . '));
						} elseif (array_key_exists(' . $slug . ', $this->getSmartLinkTypeOptions(' . $this->varExportArray($this->_options(), true) . '))) {
							switch (' . $slug . ') {
								' . implode(PHP_EOL . '				                ', $cases) . '	
							}
						}';
		} else {
			if (in_array('page', $options)) {
				$cases[] = 'case \'page\':
					if (!isset(' . $slugPage . ') || trim(' . $slugPage . ') == "" || ' . $slugPage . ' == "0" || (($page = Page::getByID(' . $slugPage . ')) && $page->error !== false)) {
						$e->add(t("The %s field for \'%s\' is required.", t("Page"), ' . $label . '));
					}
					break;';
			}
			if (in_array('file', $options)) {
				$cases[] = 'case \'file\':
					if (!isset(' . $slugFile . ') || trim(' . $slugFile . ') == "" || !is_object(File::getByID(' . $slugFile . '))) {
						$e->add(t("The %s field for \'%s\' is required.", t("File"), ' . $label . '));
					}
					break;';
			}
			if (in_array('url', $options)) {
				$cases[] = 'case \'url\':
					if (!isset(' . $slugURL . ') || trim(' . $slugURL . ') == "" || !filter_var(' . $slugURL . ', FILTER_VALIDATE_URL)) {
						$e->add(t("The %s field for \'%s\' does not have a valid URL.", t("URL"), ' . $label . '));
					}
					break;';
			}
			if (in_array('relative_url', $options)) {
				$cases[] = 'case \'relative_url\':
					if (!isset(' . $slugRelativeURL . ') || trim(' . $slugRelativeURL . ') == "") {
						$e->add(t("The %s field for \'%s\' is required.", t("Relative URL"), ' . $label . '));
					}
					break;';
			}
			if (in_array('image', $options)) {
				$cases[] = 'case \'image\':
					if (!isset(' . $slugImage . ') || trim(' . $slugImage . ') == "" || !is_object(File::getByID(' . $slugImage . '))) {
						$e->add(t("The %s field for \'%s\' is required.", t("Image"), ' . $label . '));
					}
					break;';
			}
			return 'if ((in_array("' . $this->data['slug'] . '", ' . $btFieldsRequired . ') && (!isset(' . $slug . ') || trim(' . $slug . ') == "")) || (isset(' . $slug . ') && trim(' . $slug . ') != "" && !array_key_exists(' . $slug . ', $this->getSmartLinkTypeOptions(' . $this->varExportArray($this->_options(), true) . ')))) {
			$e->add(t("The %s field has an invalid value.", ' . $label . '));
		} elseif (array_key_exists(' . $slug . ', $this->getSmartLinkTypeOptions(' . $this->varExportArray($this->_options(), true) . '))) {
			switch (' . $slug . ') {
				' . implode(PHP_EOL . '				', $cases) . '	
			}
		}';
		}
	}

	public function getSaveFunctionContents()
	{
		$repeating = $this->getRepeating();
		$repeatingTabs = $repeating ? '		' : null;
		$options = $this->_options();
		$cases = [];
		$slug = $repeating ? '$' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\']' : '$args["' . $this->data['slug'] . '"]';
		$slugPage = $repeating ? '$data[\'' . $this->data['slug'] . '_Page\']' : '$args["' . $this->data['slug'] . '_Page"]';
		$slugFile = $repeating ? '$data[\'' . $this->data['slug'] . '_File\']' : '$args["' . $this->data['slug'] . '_File"]';
		$slugURL = $repeating ? '$data[\'' . $this->data['slug'] . '_URL\']' : '$args["' . $this->data['slug'] . '_URL"]';
		$slugRelativeURL = $repeating ? '$data[\'' . $this->data['slug'] . '_Relative_URL\']' : '$args["' . $this->data['slug'] . '_Relative_URL"]';
		$slugImage = $repeating ? '$data[\'' . $this->data['slug'] . '_Image\']' : '$args["' . $this->data['slug'] . '_Image"]';
		if ($repeating) {
			$empties = [
				'page'         => $repeatingTabs . $slugPage . ' = \'0\';',
				'file'         => $repeatingTabs . $slugFile . ' = \'0\';',
				'URL'          => $repeatingTabs . $slugURL . ' = \'\';',
				'relative_URL' => $repeatingTabs . $slugRelativeURL . ' = \'\';',
				'image'        => $repeatingTabs . $slugImage . ' = \'0\';',
			];
			foreach ($empties as $k => $v) {
				if (!in_array(strtolower($k), $options)) {
					unset($empties[$k]);
				}
			}
			foreach ($empties as $k => $v) {
				if (in_array(strtolower($k), $options)) {
					$emptiesCopy = $empties;
					unset($emptiesCopy[$k]);
					$cases[] = 'case \'' . strtolower($k) . '\':
					' . $repeatingTabs . ${'slug' . ucfirst(str_replace('_', '', $k))} . ' = $' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '_' . ucfirst($k) . '\'];
					' . implode(PHP_EOL . '					', $emptiesCopy) . '
					' . $repeatingTabs . 'break;';
				}
			}
			$cases[] = 'default:
							$data[\'' . $this->data['slug'] . '\'] = \'\';
					' . implode(PHP_EOL . '					', $empties) . '
					' . $repeatingTabs . 'break;';
			return 'if (isset(' . $slug . ') && trim(' . $slug . ') != \'\') {
					$data[\'' . $this->data['slug'] . '_Title\'] = $' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '_Title\'];
					$data[\'' . $this->data['slug'] . '\'] = $' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\'];
			' . $repeatingTabs . 'switch (' . $slug . ') {
				' . $repeatingTabs . implode(PHP_EOL . '                        ', $cases) . '	
			' . $repeatingTabs . '}
		' . $repeatingTabs . '}
				else {
					$data[\'' . $this->data['slug'] . '\'] = \'\';
					$data[\'' . $this->data['slug'] . '_Title\'] = \'\';
			' . implode(PHP_EOL . '			', $empties) . '
				}';
		} else {
			if (in_array('page', $options)) {
				$cases[] = 'case \'page\':
					' . $slugFile . ' = \'0\';
					' . $slugURL . ' = \'\';
					' . $slugRelativeURL . ' = \'\';
					' . $slugImage . ' = \'0\';
					' . 'break;';
			}
			if (in_array('file', $options)) {
				$cases[] = 'case \'file\':
					' . $slugPage . ' = \'0\';
					' . $slugURL . ' = \'\';
					' . $slugRelativeURL . ' = \'\';
					' . $slugImage . ' = \'0\';
					' . 'break;';
			}
			if (in_array('url', $options)) {
				$cases[] = 'case \'url\':
					' . $slugPage . ' = \'0\';
					' . $slugRelativeURL . ' = \'\';
					' . $slugFile . ' = \'0\';
					' . $slugImage . ' = \'0\';
					' . 'break;';
			}
			if (in_array('relative_url', $options)) {
				$cases[] = 'case \'relative_url\':
					' . $slugPage . ' = \'0\';
					' . $slugURL . ' = \'\';
					' . $slugFile . ' = \'0\';
					' . $slugImage . ' = \'0\';
					' . 'break;';
			}
			if (in_array('image', $options)) {
				$cases[] = 'case \'image\':
					' . $slugPage . ' = \'0\';
					' . $slugFile . ' = \'0\';
					' . $slugURL . ' = \'\';
					' . $slugRelativeURL . ' = \'\';
					' . 'break;';
			}
			$cases[] = 'default:
					$args["' . $this->data['slug'] . '_Title"] = \'\';
					' . $slugPage . ' = \'0\';
					' . $slugFile . ' = \'0\';
					' . $slugURL . ' = \'\';
					' . $slugRelativeURL . ' = \'\';
					' . $slugImage . ' = \'0\';
					' . 'break;';
			return 'if (isset(' . $slug . ') && trim(' . $slug . ') != \'\') {
			switch (' . $slug . ') {
				' . implode(PHP_EOL . $repeatingTabs . '				', $cases) . '	
			}
		}
		else {
			$args["' . $this->data['slug'] . '_Title"] = \'\';
			' . $slugPage . ' = \'0\';
			' . $slugFile . ' = \'0\';
			' . $slugURL . ' = \'\';
			' . $slugRelativeURL . ' = \'\';
			' . $slugImage . ' = \'0\';
		}';
		}

	}

	public function getExtraFunctionsContents()
	{
		if ($this->data['ft_count'] > 0 || $this->data['ft_count_repeatable'] > 0) {
			return;
		}
		$fileService = new File();
		return $fileService->getContents($this->ftDirectory . 'elements' . DIRECTORY_SEPARATOR . 'extra_functions.txt');
	}

	public function getRepeatableUpdateItemJS()
	{
		$count = BlockDesignerProcessor::getFieldTypeVariable('smart_link', $this->data['parent']['slug']);
		BlockDesignerProcessor::setFieldTypeVariable('smart_link', $this->data['parent']['slug'], is_numeric($count) ? $count+1 : 1);
		if ($count <= 0) {
			return '    $.each($(newField).find(\'.ft-smart-link\'), function () {
            var ftSmartLinkImage = $(this).find(\'[data-link-type="image"] .ccm-file-selector\');
            if ($(ftSmartLinkImage).length > 0) {
                var ftSmartLinkImageID = $(ftSmartLinkImage).attr(\'data-file-selector-f-id\');
                $(ftSmartLinkImage).concreteFileSelector({
                    \'inputName\': $(ftSmartLinkImage).attr(\'data-file-selector-input-name\'),
                    \'filters\': [{"field":"type","type":1}],
                    \'fID\': ftSmartLinkImageID != \'0\' ? ftSmartLinkImageID : \'\'
                });
            }
            var ftSmartLinkFile = $(this).find(\'[data-link-type="file"] .ccm-file-selector\');
            if ($(ftSmartLinkFile).length > 0) {
                var ftSmartLinkFileID = $(ftSmartLinkFile).attr(\'data-file-selector-f-id\');
                $(ftSmartLinkFile).concreteFileSelector({
                    \'inputName\': $(ftSmartLinkFile).attr(\'data-file-selector-input-name\'),
                    \'filters\': [],
                    \'fID\': ftSmartLinkFileID != \'0\' ? ftSmartLinkFileID : \'\'
                });
            }
            var ftSmartLinkPage = $(this).find(\'[data-link-type="page"] [data-page-selector]\');
            if ($(ftSmartLinkPage).length > 0) {
                var ftSmartLinkcID = $(ftSmartLinkPage).attr(\'data-cID\');
                $(ftSmartLinkPage).concretePageSelector({
                    \'inputName\': $(ftSmartLinkPage).attr(\'data-input-name\'),
                    \'cID\': ftSmartLinkcID != \'0\' ? ftSmartLinkcID : \'\'
                });
            }
        });
        setTimeout(function(){
            $(container).find(\'.ft-smart-link-type\').trigger(\'change\');
        }, 200);';
		}
	}

	public function getAutoJsContents()
	{
		$repeating = $this->getRepeating();
		if (!$repeating) {
			return 'Concrete.event.bind(\'' . $this->data['btTable'] . '.' . $this->data['slug'] . '.open\', function (options, settings) {
    var container = $(\'#\' + settings.id);

    $(container).on(\'change\', \'.ft-smart-link-type\', function () {
        var me = this;
        var value = $(me).val();
        var ftSmartLink = $(me).parents(\'.ft-smart-link\');
        var ftSmartLinkOptions = $(ftSmartLink).find(\'.ft-smart-link-options\');
        var ftSmartLinkOptionsShow = false;
        if($(ftSmartLinkOptions).hasClass(\'hidden\')){
            $(ftSmartLinkOptions).removeClass(\'hidden\').hide();
        }
        $.each($(ftSmartLinkOptions).find(\'[data-link-type]\'), function () {
            if ($(this).hasClass(\'hidden\')) {
                $(this).removeClass(\'hidden\').hide();
            }
            var linkType = $(this).attr(\'data-link-type\');
            if (linkType == value) {
                $(this).slideDown();
                ftSmartLinkOptionsShow = true;
            }
            else {
                $(this).slideUp();
            }
        });
        if(ftSmartLinkOptionsShow){
            $(ftSmartLinkOptions).slideDown();
        }
        else {
            $(ftSmartLinkOptions).slideUp();
        }
    });
});';
		} else {
			$count = BlockDesignerProcessor::getFieldTypeVariable('smart_link', $this->data['parent']['slug']);
			if ($count <= 1) {
				return '$(container).on(\'change\', \'.ft-smart-link-type\', function () {
        var me = this;
        var value = $(me).val();
        var ftSmartLink = $(me).parents(\'.ft-smart-link\');
        var ftSmartLinkOptions = $(ftSmartLink).find(\'.ft-smart-link-options\');
        var ftSmartLinkOptionsShow = false;
        if($(ftSmartLinkOptions).hasClass(\'hidden\')){
            $(ftSmartLinkOptions).removeClass(\'hidden\').hide();
        }
        $.each($(ftSmartLinkOptions).find(\'[data-link-type]\'), function () {
            if ($(this).hasClass(\'hidden\')) {
                $(this).removeClass(\'hidden\').hide();
            }
            var linkType = $(this).attr(\'data-link-type\');
            if (linkType == value) {
                $(this).slideDown();
                ftSmartLinkOptionsShow = true;
            }
            else {
                $(this).slideUp();
            }
        });
        if(ftSmartLinkOptionsShow){
            $(ftSmartLinkOptions).slideDown();
        }
        else {
            $(ftSmartLinkOptions).slideUp();
        }
    });';
			}
			return '';
		}
	}

	public function getAddEditFunctionContents()
	{
		return '$this->set("' . $this->data['slug'] . '_Options", $this->getSmartLinkTypeOptions(' . $this->varExportArray($this->_options()) . ', true));';
	}

	public function getFormContents()
	{
		$repeating = $this->getRepeating();
		$btFieldsRequired = $repeating ? '$btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']' : '$btFieldsRequired';
		$slugFile = $repeating ? $this->data['slug'] . '_File' : 'ccm-b-' . $this->data['block_handle'] . '-' . $this->data['slug'] . '_File';
		$objectFile = '$' . $this->data['slug'] . '_File_o';
		$slugImage = $repeating ? $this->data['slug'] . '_Image' : 'ccm-b-' . $this->data['block_handle'] . '-' . $this->data['slug'] . '_Image';
		$objectImage = '$' . $this->data['slug'] . '_Image_o';
		$options = $this->_options();
		$optionsHTML = [];
		$js = null;
		if (!$repeating) {
			$js = PHP_EOL . PHP_EOL . '<script type="text/javascript">
	Concrete.event.publish(\'' . $this->data['btTable'] . '.' . $this->data['slug'] . '.open\', {id: \'<?php echo $' . $this->data['slug'] . '_ContainerID; ?>\'});
	$(\'#<?php echo $' . $this->data['slug'] . '_ContainerID; ?> .ft-smart-link-type\').trigger(\'change\');
</script>';
		}
		if (in_array('page', $options)) {
			$optionsHTML[] = '<div class="form-group hidden" data-link-type="page">
			' . parent::generateFormContent('label', ['slug' => $this->data['slug'] . '_Page', 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => 'Page'], $repeating) . '
            ' . parent::generateFormContent('required', [], $repeating) . '
            ' . parent::generateFormContent('page_selector', ['slug' => $this->data['slug'] . '_Page', 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null], $repeating) . '
		</div>';
		}
		if (in_array('url', $options)) {
			$optionsHTML[] = '<div class="form-group hidden" data-link-type="url">
			' . parent::generateFormContent('label', ['slug' => $this->data['slug'] . '_URL', 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => 'URL'], $repeating) . '
            ' . parent::generateFormContent('required', [], $repeating) . '
            ' . parent::generateFormContent('text', ['slug' => $this->data['slug'] . '_URL', 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null], $repeating) . '
		</div>';
		}
		if (in_array('relative_url', $options)) {
			$optionsHTML[] = '<div class="form-group hidden" data-link-type="relative_url">
			' . parent::generateFormContent('label', ['slug' => $this->data['slug'] . '_Relative_URL', 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => 'URL'], $repeating) . '
            ' . parent::generateFormContent('required', [], $repeating) . '
            ' . parent::generateFormContent('text', ['slug' => $this->data['slug'] . '_Relative_URL', 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null], $repeating) . '
		</div>';
		}
		if (in_array('file', $options)) {
			$fileGroup = null;
			$fileGroup .= '<div class="form-group hidden" data-link-type="file">';
			if (!$repeating) {
				$fileGroup .= PHP_EOL . '			<?php
			if ($' . $this->data['slug'] . '_File > 0) {
				' . $objectFile . ' = File::getByID($' . $this->data['slug'] . '_File);
				if (!is_object(' . $objectFile . ')) {
					unset(' . $objectFile . ');
				}
			} ?>';
			}
			$fileGroup .= PHP_EOL . '		    ' . parent::generateFormContent('label', ['slug' => $this->data['slug'] . '_File', 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => 'File'], $repeating) . '
            ' . parent::generateFormContent('required', [], $repeating) . '
            ' . parent::generateFormContent('file', ['slug' => $slugFile, 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'postName' => $this->data['slug'] . '_File', 'bf' => $objectFile], $repeating) . '	
		</div>';
			$optionsHTML[] = $fileGroup;
		}
		if (in_array('image', $options)) {
			$imageGroup = null;
			$imageGroup .= '<div class="form-group hidden" data-link-type="image">';
			if (!$repeating) {
				$imageGroup .= PHP_EOL . '			<?php
			if ($' . $this->data['slug'] . '_Image > 0) {
				' . $objectImage . ' = File::getByID($' . $this->data['slug'] . '_Image);
				if (!is_object(' . $objectImage . ')) {
					unset(' . $objectImage . ');
				}
			} ?>';
			}
			$imageGroup .= PHP_EOL . '			' . parent::generateFormContent('label', ['slug' => $this->data['slug'] . '_Image', 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => 'Image'], $repeating) . '
            ' . parent::generateFormContent('required', [], $repeating) . '
            ' . parent::generateFormContent('image', ['slug' => $slugImage, 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'postName' => $this->data['slug'] . '_Image', 'bf' => $objectImage], $repeating) . '
		</div>';
			$optionsHTML[] = $imageGroup;
		}
		return '<?php $' . $this->data['slug'] . '_ContainerID = \'' . $this->data['btTable'] . '-' . $this->data['slug'] . '-container-\' . $identifier_getString; ?>
<div class="ft-smart-link" id="<?php echo $' . $this->data['slug'] . '_ContainerID; ?>">
	<div class="form-group">
		' . parent::generateFormContent('label', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => $this->data['label'], 'description' => $this->data['description']], $repeating) . '
	    ' . parent::generateFormContent('required', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'array' => $btFieldsRequired]) . '
	    ' . parent::generateFormContent('select', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'options' => '$' . $this->data['slug'] . '_Options', 'attributes' => ['class' => 'form-control ft-smart-link-type']], $repeating) . '
	</div>
	
	<div class="form-group">
		<div class="ft-smart-link-options hidden" style="padding-left: 10px;">
			<div class="form-group">
				' . parent::generateFormContent('label', ['slug' => $this->data['slug'] . '_Title', 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => 'Title'], $repeating) . '
			    ' . parent::generateFormContent('text', ['slug' => $this->data['slug'] . '_Title', 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null], $repeating) . '		
			</div>
			
			' . implode(PHP_EOL . PHP_EOL . '		', $optionsHTML) . '
		</div>
	</div>
</div>
' . $js;
	}

	public function getFieldOptions()
	{
		return parent::view('field_options.php', 'block_designer_pro');
	}

	public function getDbFields()
	{
		$dbFields = [
			[
				'name' => $this->data['slug'],
				'type' => $this->getDbType(),
				'size' => 12,
			],
			[
				'name' => $this->data['slug'] . '_Title',
				'type' => 'C',
			],
		];
		$options = $this->_options();
		if (in_array('page', $options)) {
			$dbFields[] = [
				'name'       => $this->data['slug'] . '_Page',
				'type'       => 'I',
				'attributes' => [
					'default' => '0',
					'notnull' => true,
				],
			];
		}
		if (in_array('file', $options)) {
			$dbFields[] = [
				'name'       => $this->data['slug'] . '_File',
				'type'       => 'I',
				'attributes' => [
					'default' => '0',
					'notnull' => true,
				],
			];
		}
		if (in_array('image', $options)) {
			$dbFields[] = [
				'name'       => $this->data['slug'] . '_Image',
				'type'       => 'I',
				'attributes' => [
					'default' => '0',
					'notnull' => true,
				],
			];
		}
		if (in_array('url', $options)) {
			$dbFields[] = [
				'name' => $this->data['slug'] . '_URL',
				'type' => 'C',
			];
		}
		if (in_array('relative_url', $options)) {
			$dbFields[] = [
				'name' => $this->data['slug'] . '_Relative_URL',
				'type' => 'C',
			];
		}
		return $dbFields;
	}

	protected function _options()
	{
		$options = ['page', 'file', 'image', 'url', 'relative_url'];
		foreach ($options as $k => $v) {
			if (isset($this->data[$v]) && $this->data[$v] == '1') {
				unset($options[$k]);
			}
		}
		return array_values($options);
	}

	public function validate()
	{
		$options = $this->_options();
		return count($options) >= 2 ? true : t('There need to be at least 2 link options to chose from, otherwise use a default field type instead.');
	}

	public function getAssets()
	{
		$repeating = $this->getRepeating();
		$assets = [];
		if ($repeating) {
			$assets = [
				'addEdit' => [
					'require' => [
						[
							'handle' => 'core/sitemap'
						],
						[
							'handle' => 'core/file-manager'
						],
					],
				],
			];
		}
		return $assets;
	}
}