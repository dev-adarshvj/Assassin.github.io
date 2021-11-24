<?php namespace RamonLeenders\BlockDesignerPro\FieldType\FileSetFieldType;

defined('C5_EXECUTE') or die("Access Denied.");

use RamonLeenders\BlockDesigner\FieldType\FieldType;
use Concrete\Core\File\Service\File;

class FileSetFieldType extends FieldType
{
	protected $ftHandle = 'file_set';
	protected $dbType = 'C';
	protected $canRepeat = true;
	protected $uses = [
		'FileSet',
		'Concrete\Core\File\Set\SetList as FileSetList',
	];

	public function getFieldName()
	{
		return t("File Set");
	}

	public function getFieldDescription()
	{
		return t("A file set field, to display your file set title and/or files");
	}

	public function getViewContents()
	{
		$repeating = $this->getRepeating();
		$slug = $repeating ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '"]' : $this->data['slug'];
		$inner = null;
		if (isset($this->data['title_displayed']) && $this->data['title_displayed'] == '1') {
			$titleWrapper = isset($this->data['title_wrapper']) && trim($this->data['title_wrapper']) != '' && array_key_exists($this->data['title_wrapper'], $this->getTitleWrapperOptions()) ? $this->data['title_wrapper'] : 'h2';
			$inner .= sprintf('<%1$s%2$s><?php echo t($' . $slug . '->getFileSetDisplayName()); ?></%1$s>', $titleWrapper, isset($this->data['title_class']) && trim($this->data['title_class']) != '' ? sprintf(' class="%s"', $this->data['title_class']) : null);
		}
		if (isset($this->data['list_displayed']) && $this->data['list_displayed'] == '1') {
			$slugFileList = $repeating ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '_fileList"]' : $this->data['slug'] . '_fileList';
			$slugFileListUl = $repeating ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '_fileListUl"]' : $this->data['slug'] . '_fileListUl';
			$slugFileListFiles = $repeating ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '_fileListFiles"]' : $this->data['slug'] . '_fileListFiles';
			$slugFileListFile = $repeating ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '_fileListFile"]' : $this->data['slug'] . '_fileListFile';
			$inner .= '<?php
$' . $slugFileList . ' = new FileList();
$' . $slugFileList . '->filterBySet($' . $slug . ');
$' . $slugFileList . '->sortByFileSetDisplayOrder();
$' . $slugFileListFiles . ' =    $' . $slugFileList . '->getResults();
if (count($' . $slugFileListFiles . ') > 0) {
	$' . $slugFileListUl . ' = [];
	foreach($' . $slugFileListFiles . ' as $' . $slugFileListFile . '){
		$' . $slugFileListUl . '[] = sprintf(\'<li><a href="%1$s" target="_blank">%2$s</a></li>\', $' . $slugFileListFile . '->getURL(), $' . $slugFileListFile . '->getTitle());
	}
	echo sprintf(\'<ul'.(isset($this->data['list_class']) && trim($this->data['list_class']) != '' ? sprintf(' class="%s"', $this->data['list_class']) : null).'>%s</ul>\', implode(\'\', $' . $slugFileListUl . ')); 
} ?>';
		}
		return '<?php if (isset($' . $slug . ') && is_object($' . $slug . ')) { ?>' . $this->data['prefix'] . $inner . $this->data['suffix'] . '<?php } ?>';
	}

	protected function getTitleWrapperOptions()
	{
		return [
			'h1'   => t("Heading 1"),
			'h2'   => t("Heading 2"),
			'h3'   => t("Heading 3"),
			'h4'   => t("Heading 4"),
			'h5'   => t("Heading 5"),
			'h6'   => t("Heading 6"),
			'div'  => t("Div element"),
			'span' => t("Span"),
		];
	}

	public function getViewFunctionContents()
	{
		if ($this->getRepeating()) {
			return '$' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"] = isset($' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"]) && trim($' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"]) != \'\' ? FileSet::getByID($' . $this->data['parent']['slug'] . '_item_v["' . $this->data['slug'] . '"]) : false;';
		} else {
			return '$this->set("' . $this->data['slug'] . '", trim($this->' . $this->data['slug'] . ') != \'\' ? FileSet::getByID($this->' . $this->data['slug'] . ') : false);';
		}
	}

	public function getValidateFunctionContents()
	{
		if ($this->getRepeating()) {
			return 'if (in_array("' . $this->data['slug'] . '", $this->btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']) && (!isset($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']) || trim($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']) == "" || !is_object(FileSet::getByID($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\'])))) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("' . h($this->data['label']) . '"), t("' . h($this->data['parent']['label']) . '"), $' . $this->data['parent']['slug'] . '_k));
                        }';
		} else {
			return 'if (in_array("' . $this->data['slug'] . '", $this->btFieldsRequired) && (trim($args["' . $this->data['slug'] . '"]) == "" || !is_object(FileSet::getByID($args[\'' . $this->data['slug'] . '\'])))) {
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
		$slugOptions = $repeating ? $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '_options\']' : $this->data['slug'] . '_options';
		return '<div class="form-group">
    ' . parent::generateFormContent('label', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => $this->data['label'], 'description' => $this->data['description']], $repeating) . '
    ' . parent::generateFormContent('required', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'array' => $btFieldsRequired], $repeating) . '
    ' . parent::generateFormContent('select', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'options' => '$' . $slugOptions], $repeating) . '
</div>';
	}

	public function getAddEditFunctionContents()
	{
		if ($this->getRepeating()) {
			return '$' . $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '_options\'] = $this->getFileSetOptions();';
		} else {
			return '$this->set("' . $this->data['slug'] . '_options", $this->getFileSetOptions());';
		}
	}

	public function getFieldOptions()
	{
		return parent::view('field_options.php', 'block_designer_pro', ['titleWrapperOptions' => $this->getTitleWrapperOptions()]);
	}

	public function getExtraFunctionsContents()
	{
		if ($this->data['ft_count'] > 0) {
			return;
		}
		$fileService = new File();
		return $fileService->getContents($this->ftDirectory . 'elements' . DIRECTORY_SEPARATOR . 'extra_functions.txt');
	}

	public function getDbFields()
	{
		$dbFields = [
			0 => [
				'name' => $this->data['slug'],
				'type' => $this->getDbType(),
			]
		];
		if ($this->data['required']) {
			$dbFields[0]['default'] = '0';
		}
		return $dbFields;
	}
} 