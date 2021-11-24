<?php namespace RamonLeenders\BlockDesigner\FieldType\StacksFieldType;

defined('C5_EXECUTE') or die("Access Denied.");

use RamonLeenders\BlockDesigner\FieldType\FieldType;

class StacksFieldType extends FieldType
{
    protected $ftHandle = 'stacks';
    protected $uses = ['StackList', 'Stack', 'CollectionVersion', 'Database'];
    protected $canRepeat = false;

    public function getFieldDescription()
    {
        return t("A stacks field");
    }

    public function getViewContents()
    {
        $repeating = $this->getRepeating();
        $slug = $repeating ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '"]' : $this->data['slug'];
        $slugStack = $repeating ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '_stack"]' : $this->data['slug'] . '_stack';
        return '<?php
if (isset($' . $slug . ') && !empty($' . $slug . ')) { ?>' . $this->data['prefix'] . '<?php foreach ($' . $slug . ' as $' . $slugStack . ') {
        $' . $slugStack . '->display();
    } ?>' . $this->data['suffix'] . '<?php
} ?>';
    }

    public function copyFiles()
    {
        $files = [];
        if ($this->data['ft_count'] <= 0) {
            $files[] = [
                'source' => $this->ftDirectory . 'js' . DIRECTORY_SEPARATOR,
                'target' => $this->data['btDirectory'] . 'js_form' . DIRECTORY_SEPARATOR,
            ];
        }
        return $files;
    }

    private function getEntriesTableName()
    {
        $repeating = $this->getRepeating();
        return $repeating ? $this->data['btTable'] . ucFirst($this->data['parent']['slug']) . ucFirst($this->data['slug']) . 'Entries' : $this->data['btTable'] . ucFirst($this->data['slug']) . 'Entries';
    }

    public function getViewFunctionContents()
    {
        $repeating = $this->getRepeating();
        if ($repeating) {
            return '$' . $this->data['parent']['slug'] . '_item_v[\'' . $this->data['slug'] . '\'] = [];
        if ($' . $this->data['slug'] . '_entries = $db->fetchAll(\'SELECT * FROM ' . $this->getEntriesTableName($this->data) . ' WHERE bID = ? AND eID = ? ORDER BY sortOrder ASC\', [$this->bID, $' . $this->data['parent']['slug'] . '_item_v[\'id\']])) {
            foreach ($' . $this->data['slug'] . '_entries as $' . $this->data['slug'] . '_entry) {
                $' . $this->data['parent']['slug'] . '_item_v[\'' . $this->data['slug'] . '\'][$' . $this->data['slug'] . '_entry[\'stID\']] = Stack::getByID($' . $this->data['slug'] . '_entry[\'stID\']);
            }
        }';
        } else {
            return '$' . $this->data['slug'] . ' = [];
        if ($' . $this->data['slug'] . '_entries = $db->fetchAll(\'SELECT * FROM ' . $this->getEntriesTableName($this->data) . ' WHERE bID = ? ORDER BY sortOrder ASC\', [$this->bID])) {
            foreach ($' . $this->data['slug'] . '_entries as $' . $this->data['slug'] . '_entry) {
                $' . $this->data['slug'] . '[$' . $this->data['slug'] . '_entry[\'stID\']] = Stack::getByID($' . $this->data['slug'] . '_entry[\'stID\']);
            }
        }
        $this->set(\'' . $this->data['slug'] . '\', $' . $this->data['slug'] . ');';
        }
    }

    public function getAddFunctionContents()
    {
        $repeating = $this->getRepeating();
        $lines = [];
        if ($repeating) {
            $lines[] = '$' . $this->data['parent']['slug'] . '[\'stacks\'] = $this->getStacks();';
        } else {
            $lines[] = '$' . $this->data['slug'] . '_selected = [];
        $' . $this->data['slug'] . '_options = $this->getStacks();
        $this->set(\'' . $this->data['slug'] . '_options\', $' . $this->data['slug'] . '_options);
        $this->set(\'' . $this->data['slug'] . '_selected\', $' . $this->data['slug'] . '_selected);';
        }
        return implode(PHP_EOL . '        ', $lines);
    }

    public function getEditFunctionContents()
    {
        $repeating = $this->getRepeating();
        $lines = [];
        if ($repeating) {
            $lines[] = '$' . $this->data['parent']['slug'] . '[\'stacks\'] = $this->getStacks();';
        } else {
            $lines[] = '$' . $this->data['slug'] . '_selected = [];
        $' . $this->data['slug'] . '_ordered = [];
        $' . $this->data['slug'] . '_options = $this->getStacks();
        if ($' . $this->data['slug'] . '_entries = $db->fetchAll(\'SELECT * FROM ' . $this->getEntriesTableName($this->data) . ' WHERE bID = ? ORDER BY sortOrder ASC\', [$this->bID])) {
            foreach ($' . $this->data['slug'] . '_entries as $' . $this->data['slug'] . '_entry) {
                $' . $this->data['slug'] . '_selected[] = $' . $this->data['slug'] . '_entry[\'stID\'];
            }
            foreach ($' . $this->data['slug'] . '_selected as $key) {
                if (array_key_exists($key, $' . $this->data['slug'] . '_options)) {
                    $' . $this->data['slug'] . '_ordered[$key] = $' . $this->data['slug'] . '_options[$key];
                    unset($' . $this->data['slug'] . '_options[$key]);
                }
            }
            $' . $this->data['slug'] . '_options = $' . $this->data['slug'] . '_ordered + $' . $this->data['slug'] . '_options;
        }
        $this->set(\'' . $this->data['slug'] . '_options\', $' . $this->data['slug'] . '_options);
        $this->set(\'' . $this->data['slug'] . '_selected\', $' . $this->data['slug'] . '_selected);';
        }
        return implode(PHP_EOL . '        ', $lines);
    }

    public function getExtraFunctionsContents()
    {
        if ($this->data['ft_count'] <= 0) {
            return 'private function getStacks()
    {
        $stacksOptions = [];
        $stm = new StackList();
        $stm->filterByUserAdded();
        $stacks = $stm->get();
        foreach ($stacks as $st) {
            $sv = CollectionVersion::get($st, \'ACTIVE\');
            $stacksOptions[$st->getCollectionID()] = $sv->getVersionName();
        }
        return $stacksOptions;
    }';
        }
    }

    public function getDeleteFunctionContents()
    {
        return '$db->delete(\'' . $this->getEntriesTableName($this->data) . '\', [\'bID\' => $this->bID]);';
    }

    public function getDuplicateFunctionContents()
    {
        return '$' . $this->data['slug'] . '_entries = $db->fetchAll(\'SELECT * FROM ' . $this->getEntriesTableName($this->data) . ' WHERE bID = ? ORDER BY sortOrder ASC\', [$this->bID]);
        foreach ($' . $this->data['slug'] . '_entries as $' . $this->data['slug'] . '_entry) {
            unset($' . $this->data['slug'] . '_entry[\'id\']);
            $db->insert(\'' . $this->getEntriesTableName($this->data) . '\', $' . $this->data['slug'] . '_entry);
        }';
    }

    public function getSaveFunctionContents()
    {
        $repeating = $this->getRepeating();
        $entriesTableName = $this->getEntriesTableName($this->data);
        $slugEntriesDB = $repeating ? $this->data['parent']['slug'] . ucFirst($this->data['slug']) . '_entries_db' : $this->data['slug'] . '_entries_db';
        $slugEntryDBKey = $repeating ? $this->data['parent']['slug'] . ucFirst($this->data['slug']) . '_entry_db_key' : $this->data['slug'] . '_entry_db_key';
        $slugEntryDBValue = $repeating ? $this->data['parent']['slug'] . ucFirst($this->data['slug']) . '_entry_db_value' : $this->data['slug'] . '_entry_db_value';
        $slugEntries = $repeating ? $this->data['parent']['slug'] . ucFirst($this->data['slug']) . '_entries' : $this->data['slug'] . '_entries';
        $slugEntry = $repeating ? $this->data['parent']['slug'] . ucFirst($this->data['slug']) . '_entry' : $this->data['slug'] . '_entry';
        $slugQueries = $repeating ? $this->data['parent']['slug'] . ucFirst($this->data['slug']) . '_queries' : $this->data['slug'] . '_queries';
        $slugData = $repeating ? $this->data['parent']['slug'] . ucFirst($this->data['slug']) . '_data' : $this->data['slug'] . '_data';
        $slugArg = $repeating ? $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\']' : 'args[\'' . $this->data['slug'] . '\']';
        return '$' . $slugEntriesDB . ' = [];
        $' . $slugQueries . ' = [];
        if ($' . $slugEntries . ' = $db->fetchAll(\'SELECT * FROM ' . $entriesTableName . ' WHERE bID = ? ORDER BY sortOrder ASC\', [$this->bID])) {
            foreach ($' . $slugEntries . ' as $' . $slugEntry . ') {
                $' . $slugEntriesDB . '[] = $' . $slugEntry . '[\'id\'];
            }
        }
        if (isset($' . $slugArg . ') && is_array($' . $slugArg . ')) {
            $' . $this->data['slug'] . '_options = $this->getStacks();
            $i = 0;
            foreach ($' . $slugArg . ' as $stackID) {
                if ($stackID > 0 && array_key_exists($stackID, $' . $this->data['slug'] . '_options)) {
                    $' . $slugData . ' = [
                        \'stID\'      => $stackID,
                        \'sortOrder\' => $i,
                    ];
                    if (!empty($' . $slugEntriesDB . ')) {
                        $' . $slugEntryDBKey . ' = key($' . $slugEntriesDB . ');
                        $' . $slugEntryDBValue . ' = $' . $slugEntriesDB . '[$' . $slugEntryDBKey . '];
                        $' . $slugQueries . '[\'update\'][$' . $slugEntryDBValue . '] = $' . $slugData . ';
                        unset($' . $slugEntriesDB . '[$' . $slugEntryDBKey . ']);
                    } else {
                        $' . $slugData . '[\'bID\'] = $this->bID;
                        $' . $slugQueries . '[\'insert\'][] = $' . $slugData . ';
                    }
                    $i++;
                }
            }
        }
        if (!empty($' . $slugEntriesDB . ')) {
            foreach ($' . $slugEntriesDB . ' as $' . $this->data['slug'] . '_entry_db) {
                $' . $slugQueries . '[\'delete\'][] = $' . $this->data['slug'] . '_entry_db;
            }
        }
        if (!empty($' . $slugQueries . ')) {
            foreach ($' . $slugQueries . ' as $type => $values) {
                if (!empty($values)) {
                    switch ($type) {
                        case \'update\':
                            foreach ($values as $id => $data) {
                                $db->update(\'' . $entriesTableName . '\', $data, [\'id\' => $id]);
                            }
                            break;
                        case \'insert\':
                            foreach ($values as $data) {
                                $db->insert(\'' . $entriesTableName . '\', $data);
                            }
                            break;
                        case \'delete\':
                            foreach ($values as $value) {
                                $db->delete(\'' . $entriesTableName . '\', [\'id\' => $value]);
                            }
                            break;
                    }
                }
            }
        }';
    }

    public function getValidateFunctionContents()
    {
        $repeating = $this->getRepeating();
        if ($repeating) {
            // @TODO
        } else {
            return 'if (in_array("' . $this->data['slug'] . '", $this->btFieldsRequired) && (!isset($args[\'' . $this->data['slug'] . '\']) || (!is_array($args[\'' . $this->data['slug'] . '\']) || empty($args[\'' . $this->data['slug'] . '\'])))) {
            $e->add(t("The %s field is required.", t("' . h($this->data['label']) . '")));
        } else {
            $stacksPosted = 0;
            $stacksMin = ' . (isset($this->data['min_length']) && $this->data['min_length'] >= 1 ? $this->data['min_length'] : 'null') . ';
            $stacksMax = ' . (isset($this->data['max_length']) && $this->data['max_length'] >= 1 ? $this->data['max_length'] : 'null') . ';
            if (isset($args[\'' . $this->data['slug'] . '\']) && is_array($args[\'' . $this->data['slug'] . '\'])) {
                $args[\'' . $this->data['slug'] . '\'] = array_unique($args[\'' . $this->data['slug'] . '\']);
                foreach ($args[\'' . $this->data['slug'] . '\'] as $stID) {
                    if ($st = Stack::getByID($stID)) {
                        $stacksPosted++;
                    }
                }
            }
            if ($stacksMin != null && $stacksMin >= 1 && $stacksPosted < $stacksMin) {
                $e->add(t("The %s field needs a minimum of %s stacks.", t("' . h($this->data['label']) . '"), $stacksMin));
            } elseif ($stacksMax != null && $stacksMax >= 1 && $stacksMax > $stacksMin && $stacksPosted > $stacksMax) {
                $e->add(t("The %s field has a maximum of %s stacks.", t("' . h($this->data['label']) . '"), $stacksMax));
            }
        }';
        }
    }

    public function getFormContents()
    {
        $repeating = $this->getRepeating();
        $btFieldsRequired = $repeating ? '$btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']' : '$btFieldsRequired';
        $options = $repeating ? '$' . $this->data['parent']['slug'] . '[\'stacks\']' : '$' . $this->data['slug'] . '_options';
        $attributes = $repeating ? ['class' => 'form-control select2_sortable'] : [];
        $code = null;
        $code .= '<div class="form-group">
    ' . parent::generateFormContent('label', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => $this->data['label'], 'description' => $this->data['description']], $repeating) . '
    ' . parent::generateFormContent('required', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'array' => $btFieldsRequired], $repeating) . '
    ' . parent::generateFormContent('select_multiple', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'attributes' => $attributes, 'options' => $options, 'defaultValues' => 'isset($' . $this->data['slug'] . '_selected) ? $' . $this->data['slug'] . '_selected : []'], $repeating) . '
</div>';
        if (!$repeating) {
            $code .= PHP_EOL . PHP_EOL . '<script type="text/javascript">
    Concrete.event.publish(\'' . $this->data['block_handle'] . '.' . $this->data['slug'] . '.stacks\');
</script>' . PHP_EOL . PHP_EOL;
        }
        return $code;
    }

    public function getAutoCssContents()
    {
        if ($this->data['ft_count'] <= 0) {
            return '.select2-container.form-control{border: 1px solid #ccc;}';
        }
    }

    public function getAutoJsContents()
    {
        $repeating = $this->getRepeating();
        if (!$repeating) {
            return 'Concrete.event.bind(\'' . $this->data['block_handle'] . '.' . $this->data['slug'] . '.stacks\', function () {
    $(document).ready(function () {
        $(\'select[name="' . $this->data['slug'] . '[]"]\').select2_sortable();
    });
});';
        }
    }

    public function getRepeatableUpdateItemJS()
    {
        return '$(newField).find(\'select.select2_sortable\').select2_sortable();';
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
                'name'       => 'stID',
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
        if ($this->getRepeating()) {
            $fields[] = [
                'name'       => 'eID',
                'type'       => 'I',
                'attributes' => [
                    'unsigned' => true,
                ]
            ];
        }
        return [
            $this->getEntriesTableName($this->data) => [
                'fields' => $fields,
            ]
        ];
    }

    public function getFieldOptions()
    {
        return parent::view('field_options.php');
    }

    public function getAssets()
    {
        return [
            'addEdit' => [
                'register' => [
                    [
                        'type'     => 'javascript',
                        'handle'   => 'select2sortable',
                        'filename' => 'blocks/' . $this->data['block_handle'] . '/js_form/select2.sortable.js',
                    ],
                ],
                'require'  => [
                    [
                        'type'   => 'css',
                        'handle' => 'select2',
                    ],
                    [
                        'type'   => 'javascript',
                        'handle' => 'select2',
                    ],
                    [
                        'type'   => 'javascript',
                        'handle' => 'select2sortable',
                    ],
                ],
            ],
        ];
    }
}