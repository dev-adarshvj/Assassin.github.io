<?php namespace RamonLeenders\BlockDesignerPro\FieldType\RepeatableFieldType;

defined('C5_EXECUTE') or die("Access Denied.");

use RamonLeenders\BlockDesigner\BlockDesignerProcessor;
use RamonLeenders\BlockDesigner\FieldType\FieldType;
use Package;

class RepeatableFieldType extends FieldType
{
    protected $ftHandle = 'repeatable';
    protected $pkgVersionRequired = '2.6.0';
    protected $fields = [];
    protected $fieldsRequired = [];
    protected $tab;
    protected $uses = ['Database'];

    public function getFieldName()
    {
        return t("Repeatable");
    }

    public function getFieldDescription()
    {
        return t("Repeatable items");
    }

    public function getFieldsRequired()
    {
        return [$this->data['slug'] => $this->fieldsRequired];
    }

    public function getSearchableContent()
    {
        $pkg = Package::getByHandle('block_designer');
        $neededVersion = '1.3.9';
        if (!version_compare($pkg->getPackageVersion(), $neededVersion, '>=')) {
            return;
        }
        $return = null;
        $searchableContent = [];
        foreach ($this->fields as $ft) {
            if (method_exists($ft, 'getSearchableContent') && ($getSearchableContent = $ft->getSearchableContent())) {
                $searchableContent[] = $getSearchableContent;
            }
        }
        if (!empty($searchableContent)) {
            $return .= '$db = Database::connection();
        $' . $this->data['slug'] . '_items = $db->fetchAll(\'SELECT * FROM ' . $this->getTableName() . ' WHERE bID = ? ORDER BY sortOrder\', [$this->bID]);
        foreach ($' . $this->data['slug'] . '_items as $' . $this->data['slug'] . '_item_k => $' . $this->data['slug'] . '_item_v) {
            ' . implode(PHP_EOL . '            ', $searchableContent) . '
        }';
        }
        return $return;
    }

    public function getTabs()
    {
        $this->tab = 'form-' . $this->data['slug'] . '_items';
        return [
	        ['form-' . $this->data['slug'] . '_items', h($this->data['label'])],
        ];
    }

    public function getViewContents()
    {
        $return = null;
        $return .= '<?php if (!empty($' . $this->data['slug'] . '_items)) { ?>' . $this->data['prefix'] . '<?php foreach ($' . $this->data['slug'] . '_items as $' . $this->data['slug'] . '_item_key => $' . $this->data['slug'] . '_item) { ?>';
        if (isset($this->data['child_prefix']) && is_string($this->data['child_prefix']) && trim($this->data['child_prefix']) != '') {
            $return .= $this->data['child_prefix'];
        }
        foreach ($this->fields as $ft) {
            $return .= $ft->getViewContents();
        }
        if (isset($this->data['child_suffix']) && is_string($this->data['child_suffix']) && trim($this->data['child_suffix']) != '') {
            $return .= $this->data['child_suffix'];
        }
        $return .= '<?php } ?>';
        $return .= $this->data['suffix'] . '<?php } ?>';
        return $return;
    }


    public function getViewFunctionContents()
    {
        $lines = [];
        $lines[] = '$' . $this->data['slug'] . ' = [];';
        foreach ($this->fields as $ft) {
            if (method_exists($ft, 'getViewFunctionContentsExtra') && ($getViewFunctionContentsExtra = $ft->getViewFunctionContentsExtra()) && trim($getViewFunctionContentsExtra) != '') {
                $lines[] = $getViewFunctionContentsExtra;
            }
        }
        $lines[] = '$' . $this->data['slug'] . '_items = $db->fetchAll(\'SELECT * FROM ' . $this->getTableName() . ' WHERE bID = ? ORDER BY sortOrder\', [$this->bID]);';
        $vLines = [];
        foreach ($this->fields as $ft) {
            if (method_exists($ft, 'getViewFunctionContents') && ($getViewFunctionContents = $ft->getViewFunctionContents()) && trim($getViewFunctionContents) != '') {
                $vLines[] = '    ' . $getViewFunctionContents;
            }
        }
        if (!empty($vLines)) {
            $lines[] = 'foreach ($' . $this->data['slug'] . '_items as $' . $this->data['slug'] . '_item_k => &$' . $this->data['slug'] . '_item_v) {';
            $lines = array_merge($lines, $vLines);
            $lines[] = '}';
        }
        if (isset($this->data['shuffle']) && is_string($this->data['shuffle']) && $this->data['shuffle'] == '1') {
            $lines[] = 'shuffle($' . $this->data['slug'] . '_items);';
        }
        $lines[] = '$this->set(\'' . $this->data['slug'] . '_items\', $' . $this->data['slug'] . '_items);';
        $lines[] = '$this->set(\'' . $this->data['slug'] . '\', $' . $this->data['slug'] . ');';
        return implode(PHP_EOL . '        ', $lines);
    }

    public function getValidateFunctionContents()
    {
        $fieldValidation = [];
        $fieldValidationExtra = [];
        foreach ($this->fields as $ft) {
            if (method_exists($ft, 'getValidateFunctionContentsExtra')) {
                $fieldValidationExtra[] = $ft->getValidateFunctionContentsExtra();
            }
            if (method_exists($ft, 'getValidateFunctionContents')) {
                $fieldValidation[] = $ft->getValidateFunctionContents();
            }
        }
        $minLength = isset($this->data['min_length']) && is_numeric($this->data['min_length']) && $this->data['min_length'] >= 0 ? $this->data['min_length'] : 0;
        $maxLength = isset($this->data['max_length']) && is_numeric($this->data['max_length']) && $this->data['max_length'] > 0 && $this->data['max_length'] > $minLength ? $this->data['max_length'] : 0;
        return '$' . $this->data['slug'] . 'EntriesMin = ' . $minLength . ';
        $' . $this->data['slug'] . 'EntriesMax = ' . $maxLength . ';
        $' . $this->data['slug'] . 'EntriesErrors = 0;
        $' . $this->data['slug'] . ' = [];' . (!empty($fieldValidationExtra) ? PHP_EOL . '        ' . implode(PHP_EOL . '                        ', $fieldValidationExtra) : null) . '
        if (isset($args[\'' . $this->data['slug'] . '\']) && is_array($args[\'' . $this->data['slug'] . '\']) && !empty($args[\'' . $this->data['slug'] . '\'])) {
            if ($' . $this->data['slug'] . 'EntriesMin >= 1 && count($args[\'' . $this->data['slug'] . '\']) < $' . $this->data['slug'] . 'EntriesMin) {
                $e->add(t("The %s field requires at least %s entries, %s entered.", t("' . h($this->data['label']) . '"), $' . $this->data['slug'] . 'EntriesMin, count($args[\'' . $this->data['slug'] . '\'])));
                $' . $this->data['slug'] . 'EntriesErrors++;
            }
            if ($' . $this->data['slug'] . 'EntriesMax >= 1 && count($args[\'' . $this->data['slug'] . '\']) > $' . $this->data['slug'] . 'EntriesMax) {
                $e->add(t("The %s field is set to a maximum of %s entries, %s entered.", t("' . h($this->data['label']) . '"), $' . $this->data['slug'] . 'EntriesMax, count($args[\'' . $this->data['slug'] . '\'])));
                $' . $this->data['slug'] . 'EntriesErrors++;
            }
            if ($' . $this->data['slug'] . 'EntriesErrors == 0) {
                foreach ($args[\'' . $this->data['slug'] . '\'] as $' . $this->data['slug'] . '_k => $' . $this->data['slug'] . '_v) {
                    if (is_array($' . $this->data['slug'] . '_v)) {
                        ' . implode(PHP_EOL . '                        ', $fieldValidation) . '
                    } else {
                        $e->add(t("The values for the %s field, row #%s, are incomplete.", t(\'' . h($this->data['label']) . '\'), $' . $this->data['slug'] . '_k));
                    }
                }
            }
        } else {
            if ($' . $this->data['slug'] . 'EntriesMin >= 1) {
                $e->add(t("The %s field requires at least %s entries, none entered.", t("' . h($this->data['label']) . '"), $' . $this->data['slug'] . 'EntriesMin));
            }
        }';
    }

    public function validate()
    {
        $fieldSlugs = [];
        $errors = [];
        $fieldTypes = BlockDesignerProcessor::getFieldTypes();
        foreach ($this->postData['fields'] as $key => $value) {
            if (isset($value['type'], $value['repeatable']) && $value['repeatable'] == $this->data['row_id']) {
                if (array_key_exists($value['type'], $fieldTypes)) {
                    $fieldType = $fieldTypes[$value['type']];
                    /* @var $fieldTypeClass \RamonLeenders\BlockDesigner\FieldType\FieldType */
                    $fieldTypeClass = new $fieldType['namespace']($fieldType['directory'], $fieldType['pkgHandle'], $fieldType['pkgDirectory'], $fieldType['className']);
                    $fieldRepeating = $fieldTypeClass->getCanRepeat() === true && array_key_exists('repeatable', $fieldTypes) ? true : false;
                    if ($fieldRepeating) {
                        $required = isset($value['required']) && is_string($value['required']) && $value['required'] == '1' ? true : false;
	                    $fieldData = array_merge($value, [
			                    'parent'              => $this->data,
			                    'row_id'              => $key,
			                    'required'            => $required,
			                    'prefix'              => BlockDesignerProcessor::getFieldPrefix($value),
			                    'suffix'              => BlockDesignerProcessor::getFieldSuffix($value),
			                    'ft_count'            => BlockDesignerProcessor::getFieldTypeCount($value['type']),
			                    'ft_count_repeatable' => BlockDesignerProcessor::getFieldTypeCount($value['type'], true),
			                    'label'               => $value['label'],
			                    'btDirectory'         => $this->data['btDirectory'],
			                    'btTable'             => $this->data['btTable'],
			                    'block_handle'        => $this->data['block_handle'],
		                    ]
	                    );
                        if (method_exists($fieldTypeClass, 'on_start')) {
                            $fieldTypeClass->on_start($fieldData);
                        }
                        if ($fieldTypeClass->getRequiredSlug() === true) {
                            if (isset($value['slug']) && trim($value['slug']) != '') {
                                // Being sure we have a non-existing slug for the field
                                $slug_num = 1;
                                $slug = $value['slug'];
                                while (in_array($slug, $fieldSlugs) || in_array(strtolower($slug), BlockDesignerProcessor::getFieldSlugsBlacklist())) {
                                    $slug = $value['slug'] . '_' . $slug_num;
                                    $slug_num++;
                                }
                                $fieldSlugs[] = $slug;
                            } else {
                                $errors[] = t('There was no slug found for row #%s. Please try again.', $key);
                                break;
                            }
                        } else {
                            $slug = false;
                        }
                        if ($required) {
                            $this->fieldsRequired[] = $slug;
                        }
                        $fieldData['slug'] = $slug;
                        $fieldTypeClass->setRepeating(true);
                        $fieldTypeClass->setData($fieldData);
                        $continue = true;
                        if (method_exists($fieldTypeClass, 'validate') && ($validateResult = $fieldTypeClass->validate()) !== true) {
                            $errors[] = $validateResult;
                            $continue = false;
                        }
                        if ($continue) {
                            $this->fields[$key] = $fieldTypeClass;
                            BlockDesignerProcessor::updateFieldTypeCount($value['type'], true);
                        }
                    } else {
                        $errors[] = t('Row #%s (%s) does not seem to be able to repeatable.', $key, $fieldType->getFieldName());
                    }
                } else {
                    $errors[] = t('Row #%s does not seem to have an existing field type.', $key);
                }
            }
        }
        return empty($errors) ? true : $errors;
    }

    public function getFormContents()
    {
        $return = null;
        $formContents = null;
        $formContentsPre = null;
        if ($this->data['ft_count'] <= 0) {
            $return .= '<script type="text/javascript">
    var CCM_EDITOR_SECURITY_TOKEN = "<?php echo Core::make(\'helper/validation/token\')->generate(\'editor\')?>";
</script>' . PHP_EOL;
        }
        foreach ($this->fields as $ft) {
            if (method_exists($ft, 'getFormContentsPre') && ($getFormContentsPre = $ft->getFormContentsPre()) && trim($getFormContentsPre) != '') {
	            $formContentsPre .= '            ' . $getFormContentsPre;
            }
        }
        foreach ($this->fields as $ft) {
            if (method_exists($ft, 'getFormContents') && ($getFormContents = $ft->getFormContents()) && trim($getFormContents) != '') {
                $formContents .= '            ' . $getFormContents;
            }
        }
        $sortableItemClasses = ['sortable-item'];
        if (isset($this->data['no_collapse']) && is_string($this->data['no_collapse']) && $this->data['no_collapse'] == '1') {
            $sortableItemClasses[] = 'no-collapse';
        }
        $return .= $formContentsPre . '<?php $repeatable_container_id = \'' . $this->data['btTable'] . '-' . $this->data['slug'] . '-container-\' . $identifier_getString; ?>
    <div id="<?php echo $repeatable_container_id; ?>">
        <div class="sortable-items-wrapper">
            <a href="#" class="btn btn-primary add-entry">
                <?php echo t(\'' . (isset($this->data['add_entry_text']) && trim($this->data['add_entry_text']) != '' ? h($this->data['add_entry_text']) : 'Add Entry') . '\'); ?>
            </a>

            <div class="sortable-items" data-attr-content="<?php echo htmlspecialchars(
                json_encode(
                    [
                        \'items\' => $' . $this->data['slug'] . '_items,
                        \'order\' => array_keys($' . $this->data['slug'] . '_items),
                    ]
                )
            ); ?>">
            </div>

            <a href="#" class="btn btn-primary add-entry add-entry-last">
                <?php echo t(\'' . (isset($this->data['add_entry_text']) && trim($this->data['add_entry_text']) != '' ? h($this->data['add_entry_text']) : 'Add Entry') . '\'); ?>
            </a>
        </div>

        <script class="repeatableTemplate" type="text/x-handlebars-template">
            <div class="' . implode(' ', $sortableItemClasses) . '" data-id="{{id}}">
                <div class="sortable-item-title">
                    <span class="sortable-item-title-default">
                        <?php echo t(\'' . h($this->data['label']) . '\') . \' \' . t("row") . \' <span>#{{id}}</span>\'; ?>
                    </span>
                    <span class="sortable-item-title-generated"></span>
                </div>

                <div class="sortable-item-inner">' . $formContents . '</div>

                <span class="sortable-item-collapse-toggle"></span>

                <a href="#" class="sortable-item-delete" data-attr-confirm-text="<?php echo t(\'Are you sure\'); ?>">
                    <i class="fa fa-times"></i>
                </a>

                <div class="sortable-item-handle">
                    <i class="fa fa-sort"></i>
                </div>
            </div>
        </script>
    </div>

<script type="text/javascript">
    Concrete.event.publish(\'' . $this->data['btTable'] . '.' . $this->data['slug'] . '.edit.open\', {id: \'<?php echo $repeatable_container_id; ?>\'});
    $.each($(\'#<?php echo $repeatable_container_id; ?> input[type="text"].title-me\'), function () {
        $(this).trigger(\'keyup\');
    });
</script>';
        return $return;
    }

    public function getAutoCssContents()
    {
        $autoCssContents = null;
        foreach ($this->fields as $ft) {
            if (method_exists($ft, 'getAutoCssContents') && ($cssContent = $ft->getAutoCssContents()) && trim($cssContent) != '') {
                $autoCssContents .= $cssContent;
            }
        }
        return $autoCssContents;
    }

    public function getAutoJsContents()
    {
        $autoJsContents = [];
	    $deleteFunctions = [];
	    $updateFunctions = ['$(newField).find(".launch-tooltip").tooltip({container: "#ccm-tooltip-holder"});'];
        foreach ($this->fields as $ft) {
	        if (method_exists($ft, 'getRepeatableUpdateItemJS') && ($getRepeatableUpdateItemJS = $ft->getRepeatableUpdateItemJS()) && is_string($getRepeatableUpdateItemJS) && trim($getRepeatableUpdateItemJS) != '') {
		        $updateFunctions[] = $getRepeatableUpdateItemJS;
	        }
	        if (method_exists($ft, 'getRepeatableDeleteItemJS') && ($getRepeatableDeleteItemJS = $ft->getRepeatableDeleteItemJS()) && is_string($getRepeatableDeleteItemJS) && trim($getRepeatableDeleteItemJS) != '') {
		        $deleteFunctions[] = $getRepeatableDeleteItemJS;
	        }
	        if (method_exists($ft, 'getAutoJsContents') && ($getAutoJsContents = $ft->getAutoJsContents()) && is_string($getAutoJsContents) && $getAutoJsContents != '') {
		        $autoJsContents[] = $ft->getAutoJsContents();
	        }
        }
        return 'Concrete.event.bind(\'' . $this->data['btTable'] . '.' . $this->data['slug'] . '.edit.open\', function (options, settings) {
    var container = $(\'#\' + settings.id);
    var hbTemplate = Handlebars.compile($(container).find(\'.repeatableTemplate\').html());
    var sortableItems = $(container).find(\'.sortable-items\');
    var slideSpeed = 200;
    var sortableConfig = {
        items: \'.sortable-item\',
        handle: \'.sortable-item-handle\',
        forcePlaceholderSize: true,
        placeholder: \'sortable-item sortable-item-placeholder\',
        start: function (e, ui) {
            ui.placeholder.height(ui.item.height());
        }
    };

    var token = function () {
        return Math.random().toString(36).substr(2) + Math.random().toString(36).substr(2);
    };

    var updateItem = function (newField) {
        ' . (!empty($updateFunctions) ? implode(PHP_EOL . '    ', $updateFunctions) : null) . '
    };

     var loadComplete = function () {
        var sortableItemsLength = $(sortableItems).find(\'.sortable-item\').length;
        if (sortableItemsLength <= 0) {
            $(container).find(\'.add-entry-last\').addClass(\'hidden\');
        }
        else {
            $(container).find(\'.add-entry-last\').removeClass(\'hidden\');
        }
    };

    $(document).ready(function () {
        var jsonString = $(sortableItems).attr(\'data-attr-content\');
        var data = $.parseJSON(jsonString);
        if ($.isPlainObject(data)) {
            var items = [];
            var newField = false;
            $.each(data.order, function (i, value) {
                var item = data.items[value];
                if (item != undefined) {
                    item.token = token();
                    item.id = value + 1;
                    items.push(item);
                }
            });
            $.each(items, function (i, v) {
                $(sortableItems).append(hbTemplate(v));
                newField = $(sortableItems).find(\'.sortable-item[data-id="\' + v.id + \'"]\');
                updateItem(newField);
            });
            loadComplete();
            $(sortableItems).sortable(sortableConfig);
        }
    });

    $(container).on(\'click\', \'.sortable-item .sortable-item-collapse-toggle\', function (e) {
        e.preventDefault();
        var sortableItem = $(this).parent();
        if ($(sortableItem).hasClass(\'collapsed\')) {
            $(sortableItem).removeClass(\'collapsed\');
        }
        else {
            $(sortableItem).addClass(\'collapsed\');
        }
    });

    $(container).on(\'click\', \'.sortable-item .sortable-item-delete\', function (e) {
        e.preventDefault();
        var deleteIt = confirm($(this).attr(\'data-attr-confirm-text\'));
        if (deleteIt === true) {
            var sortableItem = $(this).parent();
            $(sortableItem).slideUp(slideSpeed, function () {
                ' . (!empty($deleteFunctions) ? implode(PHP_EOL . '                ', $deleteFunctions) . PHP_EOL . '                ' : null) . '$(sortableItem).remove();
                loadComplete();
            });
        }
    });

    $(container).on(\'keyup\', \'.sortable-item input[type="text"].title-me\', function (e) {
        var me = this;
        var value = $(me).val();
        var sortableItem = $(me).parents(\'.sortable-item\');
        var newFieldTitle = $(sortableItem).find(\'.sortable-item-title\');
        var newFieldTitleDefault = $(newFieldTitle).find(\'.sortable-item-title-default\');
        var newFieldTitleGenerated = $(newFieldTitle).find(\'.sortable-item-title-generated\');
        if ($.trim(value) != \'\') {
            $(newFieldTitleDefault).hide();
            $(newFieldTitleGenerated).html(value).show();
        }
        else {
            $(newFieldTitleDefault).show();
            $(newFieldTitleGenerated).html(\' \').hide();
        }
    });

    $(container).on(\'click\', \'.add-entry\', function (e) {
        e.preventDefault();
        var ids = new Array();
        $(sortableItems).find(\'.sortable-item\').each(function () {
            ids.push(parseInt($(this).attr(\'data-id\')));
        });
        if (ids.length == 0) {
            ids.push(0);
        }
        var id = Math.max.apply(Math, ids) + 1;
        var data = {
            "token": token(),
            "id": id,
            "sortOrder": id
        };
        $(sortableItems).'.(isset($this->data['prepend']) && is_string($this->data['prepend']) && $this->data['prepend'] == '1' ? 'prepend' : 'append').'(hbTemplate(data)).sortable(sortableConfig);
        var newField = $(sortableItems).find(\'.sortable-item[data-id="\' + id + \'"]\');
        $.each($(newField).find(\'input[data-attr-default-value], select[data-attr-default-value], textarea[data-attr-default-value]\'), function(){
           $(this).val($(this).attr(\'data-attr-default-value\'));
        });
        $(newField).hide().slideDown(slideSpeed);
        loadComplete();
        $(newField).find(\'input, textarea, select\').filter(\':visible:first\').focus();
        updateItem(newField);
        var uiDialogContent = $(container).parents(\'.ui-dialog-content\');
        $(uiDialogContent).animate({scrollTop: $(newField).position().top + $(uiDialogContent).scrollTop()});
    });' . (!empty($autoJsContents) ? PHP_EOL . PHP_EOL . '    ' . implode(PHP_EOL . '    ', $autoJsContents) : null) . '
});';
    }

    public function getSaveFunctionContents()
    {
        $saveFunctions = [];
        foreach ($this->fields as $ft) {
            if (method_exists($ft, 'getSaveFunctionContents')) {
                $saveFunctions[] = $ft->getSaveFunctionContents();
            }
        }
        return '$rows = $db->fetchAll(\'SELECT * FROM ' . $this->getTableName() . ' WHERE bID = ? ORDER BY sortOrder\', [$this->bID]);
        $' . $this->data['slug'] . '_items = isset($args[\'' . $this->data['slug'] . '\']) && is_array($args[\'' . $this->data['slug'] . '\']) ? $args[\'' . $this->data['slug'] . '\'] : [];
        $queries = [];
        if (!empty($' . $this->data['slug'] . '_items)) {
            $i = 0;
            foreach ($' . $this->data['slug'] . '_items as $' . $this->data['slug'] . '_item) {
                $data = [
                    \'sortOrder\' => $i + 1,
                ];
                ' . implode(PHP_EOL . '                ', $saveFunctions) . '
                if (isset($rows[$i])) {
                    $queries[\'update\'][$rows[$i][\'id\']] = $data;
                    unset($rows[$i]);
                } else {
                    $data[\'bID\'] = $this->bID;
                    $queries[\'insert\'][] = $data;
                }
                $i++;
            }
        }
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $queries[\'delete\'][] = $row[\'id\'];
            }
        }
        if (!empty($queries)) {
            foreach ($queries as $type => $values) {
                if (!empty($values)) {
                    switch ($type) {
                        case \'update\':
                            foreach ($values as $id => $data) {
                                $db->update(\'' . $this->getTableName() . '\', $data, [\'id\' => $id]);
                            }
                            break;
                        case \'insert\':
                            foreach ($values as $data) {
                                $db->insert(\'' . $this->getTableName() . '\', $data);
                            }
                            break;
                        case \'delete\':
                            foreach ($values as $value) {
                                $db->delete(\'' . $this->getTableName() . '\', [\'id\' => $value]);
                            }
                            break;
                    }
                }
            }
        }';
    }

    public function getExtraOptions()
    {
        return parent::view('extra_options.php', 'block_designer_pro');
    }

    public function getFieldOptions()
    {
        return parent::view('field_options.php', 'block_designer_pro');
    }

    public function getOnStartFunctionContents()
    {
        $return = null;
        foreach ($this->fields as $ft) {
            if (method_exists($ft, 'getOnStartFunctionContents')) {
                $return .= $ft->getOnStartFunctionContents();
            }
        }
        return $return;
    }

    public function getAddEditFunctionContents()
    {
        $lines = [];
        $lines[] = '$' . $this->data['slug'] . ' = [];';
        foreach ($this->fields as $ft) {
            if (method_exists($ft, 'getAddEditFunctionContents')) {
                $lines[] = '        ' . $ft->getAddEditFunctionContents();
            }
        }
        $lines[] = '        $this->set(\'' . $this->data['slug'] . '\', $' . $this->data['slug'] . ');';
        if ($this->data['ft_count'] <= 0) {
            $lines[] = '        $this->set(\'identifier\', new \Concrete\Core\Utility\Service\Identifier());';
        }
        return implode(PHP_EOL, $lines);
    }

    public function getAddFunctionContents()
    {
        $lines = [];
        $lines[] = '$' . $this->data['slug'] . ' = $this->get(\'' . $this->data['slug'] . '\');';
        $lines[] = '        ' . '$this->set(\'' . $this->data['slug'] . '_items\', []);';
        foreach ($this->fields as $ft) {
            if (method_exists($ft, 'getAddFunctionContents')) {
                $lines[] = '        ' . $ft->getAddFunctionContents();
            }
        }
        $lines[] = '        $this->set(\'' . $this->data['slug'] . '\', $' . $this->data['slug'] . ');';
        return implode(PHP_EOL, $lines);
    }

    public function getEditFunctionContents()
    {
        $lines = [];
        $lines[] = '$' . $this->data['slug'] . ' = $this->get(\'' . $this->data['slug'] . '\');';
        $lines[] = '        $' . $this->data['slug'] . '_items = $db->fetchAll(\'SELECT * FROM ' . $this->getTableName() . ' WHERE bID = ? ORDER BY sortOrder\', [$this->bID]);';
        foreach ($this->fields as $ft) {
            if (method_exists($ft, 'getEditFunctionContents')) {
                $lines[] = '        ' . $ft->getEditFunctionContents();
            }
        }
        $lines[] = '        $this->set(\'' . $this->data['slug'] . '\', $' . $this->data['slug'] . ');';
        $lines[] = '        $this->set(\'' . $this->data['slug'] . '_items\', $' . $this->data['slug'] . '_items);';
        return implode(PHP_EOL, $lines);
    }

    public function getDeleteFunctionContents()
    {
        $return = '$db->delete(\'' . $this->getTableName() . '\', [\'bID\' => $this->bID]);';
        foreach ($this->fields as $ft) {
            if (method_exists($ft, 'getDeleteFunctionContents')) {
                $return .= '
        ' . $ft->getDeleteFunctionContents();
            }
        }
        return $return;
    }

    public function getDuplicateFunctionContents()
    {
        $return = '$' . $this->data['slug'] . '_items = $db->fetchAll(\'SELECT * FROM ' . $this->getTableName() . ' WHERE bID = ? ORDER BY sortOrder\', [$this->bID]);
        foreach ($' . $this->data['slug'] . '_items as $' . $this->data['slug'] . '_item) {
            unset($' . $this->data['slug'] . '_item[\'id\']);
            $' . $this->data['slug'] . '_item[\'bID\'] = $newBID;
            $db->insert(\'' . $this->getTableName() . '\', $' . $this->data['slug'] . '_item);
        }';
        foreach ($this->fields as $ft) {
            if (method_exists($ft, 'getDuplicateFunctionContents')) {
                $return .= '
        ' . $ft->getDuplicateFunctionContents();
            }
        }
        return $return;
    }

    public function getExtraFunctionsContents()
    {
        $lines = [];
        foreach ($this->fields as $ft) {
            if (method_exists($ft, 'getExtraFunctionsContents') && ($extraFunctions = $ft->getExtraFunctionsContents()) && trim($extraFunctions) != '') {
                $lines[] = '    ' . $extraFunctions;
            }
        }
        return implode(PHP_EOL . PHP_EOL, $lines);
    }

    public function getUses()
    {
        $return = $this->uses;
        foreach ($this->fields as $ft) {
            if (($uses = $ft->getUses()) && is_array($uses) && !empty($uses)) {
                $return = array_merge($return, $uses);
            }
        }
        return $return;
    }

    public function copyFiles()
    {
        $files = [];
        if ($this->data['ft_count'] <= 0) {
            $files[] = [
	            'source' => $this->ftDirectory . 'css' . DIRECTORY_SEPARATOR,
	            'target' => $this->data['btDirectory'] . 'css_form' . DIRECTORY_SEPARATOR,
            ];
            $files[] = [
	            'source' => $this->ftDirectory . 'js' . DIRECTORY_SEPARATOR,
	            'target' => $this->data['btDirectory'] . 'js_form' . DIRECTORY_SEPARATOR,
            ];
        }
        foreach ($this->fields as $ft) {
            if (method_exists($ft, 'copyFiles')) {
                $files = array_merge($files, $ft->copyFiles());
            }
        }
        return $files;
    }

    public function getBtExportTables()
    {
        return [
	        $this->getTableName()
        ];
    }

    private function getTableName()
    {
        return $this->data['btTable'] . $this->nameSpaceMe($this->data['slug'] . '_entries');
    }

    private function nameSpaceMe($name)
    {
        $nameSpaced = implode('', array_map(function ($v, $k) {
            return ucfirst($v);
        }, explode('_', $name), array_keys(explode('_', $name))));
        return $nameSpaced;
    }

    public function getDbTables()
    {
	    $fields = [
		    [
			    'name'       => 'id',
			    'type'       => 'I',
			    'attributes' => [
				    'key'           => true,
				    'unsigned'      => true,
				    'autoincrement' => true,
			    ]
		    ],
		    [
			    'name'       => 'bID',
			    'type'       => 'I',
			    'attributes' => [
				    'unsigned' => true,
			    ]
		    ],
		    [
			    'name' => 'sortOrder',
			    'type' => 'I',
		    ],
	    ];
        foreach ($this->fields as $ft) {
            if (method_exists($ft, 'getDbFields')) {
                $fields = array_merge($fields, $ft->getDbFields());
            }
        }
	    $tables = [
		    $this->getTableName($this->data) => [
			    'fields' => $fields
		    ]
	    ];
        foreach ($this->fields as $ft) {
            if (method_exists($ft, 'getDbTables')) {
                $tables = array_merge($tables, $ft->getDbTables());
            }
        }
        return $tables;
    }

    public function getAssets()
    {
        $assets = [
	        'addEdit' => [
		        'register' => [
			        [
				        'type'     => 'css',
				        'handle'   => 'repeatable-ft.form',
				        'filename' => 'blocks/' . $this->data['block_handle'] . '/css_form/repeatable-ft.form.css',
			        ],
			        [
				        'type'     => 'javascript',
				        'handle'   => 'handlebars',
				        'filename' => 'blocks/' . $this->data['block_handle'] . '/js_form/handlebars-v4.0.4.js',
			        ],
			        [
				        'type'     => 'javascript',
				        'handle'   => 'handlebars-helpers',
				        'filename' => 'blocks/' . $this->data['block_handle'] . '/js_form/handlebars-helpers.js',
			        ],
		        ],
		        'require'  => [
			        [
				        'handle' => 'core/sitemap',
			        ],
			        [
				        'type'   => 'css',
				        'handle' => 'repeatable-ft.form',
			        ],
			        [
				        'type'   => 'javascript',
				        'handle' => 'handlebars',
			        ],
			        [
				        'type'   => 'javascript',
				        'handle' => 'handlebars-helpers',
			        ],
		        ],
	        ],
        ];
        foreach ($this->fields as $ft) {
            if (method_exists($ft, 'getAssets') && ($ftAssets = $ft->getAssets()) && is_array($ftAssets) && !empty($ftAssets)) {
                $assets = array_merge_recursive($assets, $ftAssets);
            }
        }
        return $assets;
    }

	public function getBtExportFileColumn()
	{
		$fileColumns = [];
		foreach ($this->fields as $ft) {
			if (method_exists($ft, 'getBtExportFileColumn') && ($btExportFileColumns = $ft->getBtExportFileColumn()) && is_array($btExportFileColumns) && !empty($btExportFileColumns)) {
				$fileColumns = array_merge_recursive($fileColumns, $btExportFileColumns);
			}
		}
		return $fileColumns;
	}

	public function getBtExportPageColumn()
	{
		$pageColumns = [];
		foreach ($this->fields as $ft) {
			if (method_exists($ft, 'getBtExportPageColumn') && ($btExportPageColumns = $ft->getBtExportPageColumn()) && is_array($btExportPageColumns) && !empty($btExportPageColumns)) {
				$pageColumns = array_merge_recursive($pageColumns, $btExportPageColumns);
			}
		}
		return $pageColumns;
	}
}