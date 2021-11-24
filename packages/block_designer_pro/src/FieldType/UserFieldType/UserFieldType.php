<?php namespace RamonLeenders\BlockDesignerPro\FieldType\UserFieldType;

defined('C5_EXECUTE') or die("Access Denied.");

use RamonLeenders\BlockDesigner\FieldType\FieldType;

class UserFieldType extends FieldType
{
    protected $ftHandle = 'user';
    protected $dbType = 'I';
    protected $uses = ['UserList', 'UserInfo'];
    protected $canRepeat = true;
    protected $pkgVersionRequired = '2.0.0';

    public function getFieldDescription()
    {
        return t("An user select field");
    }

    public function getViewContents()
    {
        $outputOptions = [
	        'uID'    => 'getUserID()',
	        'uName'  => 'getUserDisplayName()',
	        'uEmail' => 'getUserEmail()',
        ];
        $slug = $this->getRepeating() ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '"]' : $this->data['slug'];
        $output = '<?php echo $' . $slug . '->' . (isset($this->data['output']) && is_string($this->data['output']) && array_key_exists($this->data['output'], $outputOptions) ? $outputOptions[$this->data['output']] : 'getUserDisplayName()') . '; ?>';
        if (isset($this->data['link']) && is_string($this->data['link']) && $this->data['link'] == '1') {
            $output = '<a href="<?php echo URL::to(\'/members/profile\',\'view\', $' . $slug . '->getUserID())?>"' . (isset($this->data['link_class']) && is_string($this->data['link_class']) && trim($this->data['link_class']) != '' ? ' class="' . $this->data['link_class'] . '"' : null) . '>' . $output . '</a>';
        }
        return '<?php if (isset($' . $slug . ') && is_object($' . $slug . ')) { ?>' . $this->data['prefix'] . $output . $this->data['suffix'] . '<?php } ?>';
    }

    public function getViewFunctionContents()
    {
        if ($this->getRepeating()) {
            return '$' . $this->data['parent']['slug'] . '_item_v[\'' . $this->data['slug'] . '\'] = UserInfo::getByID($' . $this->data['parent']['slug'] . '_item_v[\'' . $this->data['slug'] . '\']);';
        } else {
            return '$this->set(\'' . $this->data['slug'] . '\', UserInfo::getByID($this->' . $this->data['slug'] . '));';
        }
    }

    public function getValidateFunctionContents()
    {
        if ($this->getRepeating()) {
            return 'if (in_array("' . $this->data['slug'] . '", $this->btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']) && (!isset($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']) || trim($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']) == "")) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("' . h($this->data['label']) . '"), t("' . h($this->data['parent']['label']) . '"), $' . $this->data['parent']['slug'] . '_k));
                        } elseif (isset($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']) && trim($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']) != "") {
                            if (!is_object(UserInfo::getByID($' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']))) {
                                $e->add(t("The %s field does not contain an existing user (%s, row #%s).", t("' . h($this->data['label']) . '"), t("' . h($this->data['parent']['label']) . '"), $' . $this->data['parent']['slug'] . '_k));
                            }
                        }';
        } else {
            return 'if (in_array("' . $this->data['slug'] . '", $this->btFieldsRequired) && trim($args["' . $this->data['slug'] . '"]) == "") {
            $e->add(t("The %s field is required.", t("' . h($this->data['label']) . '")));
        } elseif (trim($args["' . $this->data['slug'] . '"]) != "") {
            if (!is_object(UserInfo::getByID($args["' . $this->data['slug'] . '"]))) {
                $e->add(t("The %s field does not contain an existing user.", t("' . h($this->data['label']) . '")));
            }
        }';
        }
    }

    public function getAddEditFunctionContents()
    {
        $repeating = $this->getRepeating();
        $btFieldsRequired = $repeating ? '$this->btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']' : '$this->btFieldsRequired';
        $userList = $this->data['slug'] . '_UserList';
        $users = $this->data['slug'] . '_Users';
        $user = $this->data['slug'] . '_User';
        $sorting = [
	        'uID'   => 'sortByUserID',
	        'uName' => 'sortByUserName',
        ];
        $lines = [
	        '$' . $this->data['slug'] . '_options = [];
        if (!in_array("' . $this->data['slug'] . '", ' . $btFieldsRequired . ')) {
            $' . $this->data['slug'] . '_options[\'\'] = \'-- \' . t(\'None\') . \' --\';
        }
        $' . $userList . ' = new UserList();'
        ];
        if (isset($this->data['sort']) && is_string($this->data['sort']) && array_key_exists($this->data['sort'], $sorting)) {
            $lines[] = '$' . $userList . '->' . $sorting[$this->data['sort']] . '();';
        }
        if (isset($this->data['unvalidated']) && $this->data['unvalidated'] == '1') {
            $lines[] = '$' . $userList . '->includeUnvalidatedUsers();';
        }
        if (isset($this->data['inactive']) && $this->data['inactive'] == '1') {
            $lines[] = '$' . $userList . '->includeInactiveUsers();';
        }
        $lines[] = '$' . $users . ' = $' . $userList . '->getResults();';
        $lines[] = 'foreach ($' . $users . ' as $' . $user . ') {
            $' . $this->data['slug'] . '_options[$' . $user . '->getUserID()] = $' . $user . '->getUserDisplayName() . \' - \' . $' . $user . '->getUserEmail();
        }';
        if ($repeating) {
            $lines[] = '$' . $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '_options\'] = $' . $this->data['slug'] . '_options;';
        } else {
            $lines[] = '$this->set(\'' . $this->data['slug'] . '_options\', $' . $this->data['slug'] . '_options);';
        }
        return implode(PHP_EOL . '        ', $lines);
    }

    public function getFormContents()
    {
        $repeating = $this->getRepeating();
        $btFieldsRequired = $repeating ? '$btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']' : '$btFieldsRequired';
        $options = $repeating ? '$' . $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '_options\']' : '$' . $this->data['slug'] . '_options';
        return '<div class="form-group">
    ' . parent::generateFormContent('label', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => $this->data['label'], 'description' => $this->data['description']], $repeating) . '
    ' . parent::generateFormContent('required', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'array' => $btFieldsRequired], $repeating) . '
    ' . parent::generateFormContent('select', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'options' => $options], $repeating) . '
</div>';
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
			    'size' => 11,
		    ]
	    ];
    }
}