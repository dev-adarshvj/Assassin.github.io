<?php namespace RamonLeenders\BlockDesignerPro\FieldType\PageAttributeFieldType;

defined('C5_EXECUTE') or die("Access Denied.");

use RamonLeenders\BlockDesigner\FieldType\FieldType;

class PageAttributeFieldType extends FieldType
{
    protected $ftHandle = 'page_attribute';
    protected $dbType = 'I';
    protected $uses = ['Page'];
    protected $canRepeat = true;
    protected $requiredSlug = true;
    protected $pkgVersionRequired = '1.2.6';

    public function getFieldName()
    {
        return t("Page Attribute");
    }

    public function getFieldDescription()
    {
        return t("A page attribute you pre-select will be outputted in your view file.");
    }

    public function on_start($data)
    {
        if (isset($data['current_page']) && $data['current_page'] == '1') {
            $this->requiredSlug = false;
        }
    }

    public function getPageAttributes()
    {
        return [
	        'getCollectionName'                => t('Name'),
	        'getCollectionLink'                => t('Link'),
	        'getPackageID'                     => t('Package ID (page that is added by a package, returns 0 if its not in a package)'),
	        'getPackageHandle'                 => t('Package handle (page that is added by a package)'),
	        'getCollectionPath'                => t('Path'),
	        'getCollectionUserID'              => t('uID for a page owner'),
	        'getCollectionHandle'              => t('Handle'),
	        'getPageTypeName'                  => t('Page Type name'),
	        'getPageTypeID'                    => t('Page Type ID'),
	        'getPageTypeHandle'                => t('Page Type handle'),
	        'getPageTemplateID'                => t('Page Template ID'),
	        'getPageTemplateHandle'            => t('Page Template handle'),
	        'getCollectionThemeID'             => t('Theme ID'),
	        'getCollectionPointerID'           => t('Collection ID for the aliased page (returns 0 unless used on an actual alias)'),
	        'getCollectionPointerExternalLink' => t('Link for the aliased page'),
	        'getCollectionPointerOriginalID'   => t('The original cID of a page'),
	        'getCollectionFilename'            => t('File name of a page (single pages)'),
	        'getCollectionDatePublic'          => t('Date the current version was made public (formated like: %s)', '2009-01-01 00:00:00'),
	        'getCollectionDescription'         => t('Description'),
	        'getCollectionParentID'            => t('cID of the page his parent'),
	        'getCollectionDisplayOrder'        => t('The position of the page in the sitemap'),
	        'getNumChildren'                   => t('Number of children'),
	        'getNumChildrenDirect'             => t('Number of direct children'),
	        'getPageWrapperClass'              => t('Wrapper class'),
	        'getPageIndexScore'                => t('Index score'),
	        'getPageIndexContent'              => t('Index content'),
	        'other'                            => t('Other'),
        ];
    }

    public function validate()
    {
        $pageAttributes = $this->getPageAttributes();
        $errors = [];
        if (!array_key_exists($this->data['attribute'], $pageAttributes)) {
            $errors[] = t('An invalid page attribute has been entered for row #%s.', $this->data['row_id']);
        } else {
            if ($this->data['attribute'] == 'other' && (!isset($this->data['attribute_other']) || trim($this->data['attribute_other']) == '')) {
                $errors[] = t('An invalid custom page attribute has been entered for row #%s.', $this->data['row_id']);
            }
        }
        return empty($errors) ? true : implode('<br/>', $errors);
    }

    public function getValidateFunctionContents()
    {
        if ($this->selectPage()) {
            if ($this->getRepeating()) {
                $btFieldsRequired = '$this->btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']';
                $slug = '$' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']';
                return 'if ((in_array("' . $this->data['slug'] . '", ' . $btFieldsRequired . ') || (isset(' . $slug . ') && trim(' . $slug . ') != \'\')) && (trim(' . $slug . ') == "" || ' . $slug . ' == "0" || (($page = Page::getByID(' . $slug . ')) && $page->error !== false))) {
                            $e->add(t("The %s field is required (%s, row #%s).", "' . h($this->data['label']) . '", "' . h($this->data['parent']['label']) . '", $' . $this->data['parent']['slug'] . '_k));
                        }';
            } else {
                return 'if (in_array("' . $this->data['slug'] . '", $this->btFieldsRequired) && (trim($args["' . $this->data['slug'] . '"]) == "" || $args["' . $this->data['slug'] . '"] == "0" || (($page = Page::getByID($args["' . $this->data['slug'] . '"])) && $page->error !== false))) {
            $e->add(t("The %s field is required.", "' . h($this->data['label']) . '"));
        }';
            }
        }
    }

    public function getFieldOptions()
    {
        return parent::view('field_options.php', 'block_designer_pro', ['pageAttributes' => $this->getPageAttributes()]);
    }

    public function getSaveFunctionContents()
    {
        if ($this->selectPage() && $this->getRepeating()) {
            return 'if (isset($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\']) && trim($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\']) != \'\') {
                    $data[\'' . $this->data['slug'] . '\'] = trim($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\']);
                } else {
                    $data[\'' . $this->data['slug'] . '\'] = null;
                }';
        }
    }

    public function getFormContents()
    {
        if ($this->selectPage()) {
            $repeating = $this->getRepeating();
            $btFieldsRequired = $repeating ? '$btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']' : '$btFieldsRequired';
            $html = '<div class="form-group">
    ' . parent::generateFormContent('label', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => $this->data['label'], 'description' => $this->data['description']], $repeating) . '
    ' . parent::generateFormContent('required', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'array' => $btFieldsRequired], $repeating) . '
    ' . parent::generateFormContent('page_selector', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'attributes' => ['class' => 'page_attribute-ft']], $repeating);
            $html .= '</div>';
            return $html;
        }
    }

    private function selectPage()
    {
        return !isset($this->data['current_page']) || $this->data['current_page'] != '1' ? true : false;
    }

    public function getRepeatableUpdateItemJS()
    {
        if ($this->selectPage()) {
            return 'var pageSelector = $(newField).find(\'.page_attribute-ft\');
        if ($(pageSelector).length > 0) {
            $(pageSelector).concretePageSelector({inputName: $(pageSelector).attr(\'data-input-name\'), cID : $(pageSelector).attr(\'data-cID\')});
        }';
        }
    }

    public function getViewContents()
    {
        $pageFunction = $this->data['attribute'] == 'other' ? '->getAttribute(\'' . $this->data['attribute_other'] . '\', \'display\')' : '->' . $this->data['attribute'] . '()';
        if ($this->selectPage()) {
            $slug = $this->getRepeating() ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '"]' : $this->data['slug'];
            return '<?php if (!empty($' . $slug . ') && ($linkToC = Page::getByID($' . $slug . '))) {
    ?>' . $this->data['prefix'] . '<?php echo $linkToC' . $pageFunction . ';
?>' . $this->data['suffix'] . '<?php } ?>';
        } else {
            if ($this->data['attribute'] == 'other') {
                return '<?php echo Page::getCurrentPage()' . $pageFunction . '; ?>';
            } else {
                return '<?php echo Page::getCurrentPage()' . $pageFunction . '; ?>';
            }
        }
    }

    public function getDbFields()
    {
        if ($this->selectPage()) {
            return [
	            0 => [
		            'name' => $this->data['slug'],
		            'type' => $this->getDbType(),
	            ],
            ];
        }
    }
}