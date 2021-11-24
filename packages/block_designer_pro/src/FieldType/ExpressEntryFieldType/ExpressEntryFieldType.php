<?php namespace RamonLeenders\BlockDesignerPro\FieldType\ExpressEntryFieldType;

defined('C5_EXECUTE') or die("Access Denied.");

use RamonLeenders\BlockDesigner\FieldType\FieldType;

class ExpressEntryFieldType extends FieldType
{
    protected $ftHandle = 'express_entry';
    protected $dbType = 'I';
    protected $canRepeat = true;
    protected $uses = ['Database'];
    protected $appVersionRequired = '8.0.0a3';

    public function getFieldName()
    {
        return t("Express Entry");
    }

    public function getFieldDescription()
    {
        return t("An Express (Entity) selector");
    }

    public function validate()
    {
        return isset($this->data['entity_handle']) && trim($this->data['entity_handle']) != '' ? true : t('No entity handle was entered for row #%s.', $this->data['row_id']);
    }

    public function getViewContents()
    {
        if ($this->getRepeating()) {
            return '<?php if (isset($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '_entry\']) && $' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '_entry\']) {
                // echo $' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '_entry\']->getAttribute(\'your_attribute_handle\', \'display\');
            } ?>';
        } else {
            return '<?php if ($' . $this->data['slug'] . '_entry) {
                // echo $' . $this->data['slug'] . '_entry->getAttribute(\'your_attribute_handle\', \'display\');
            } ?>';
        }
    }

    public function getViewFunctionContentsExtra()
    {
        $lines = [];
        if ($this->data['ft_count'] <= 0 && $this->data['ft_count_repeatable'] <= 0) {
            $lines[] = '$entity_Manager = Core::make(\'database/orm\')->entityManager();';
            $lines[] = '$this->set(\'entity_Manager\', $entity_Manager);';
        }
        return implode(PHP_EOL . '        ', $lines);
    }

    public function getViewFunctionContents()
    {
        $lines = [];
        if ($this->getRepeating()) {
            $slug = '$' . $this->data['parent']['slug'] . '_item_v[\'' . $this->data['slug'] . '\']';
            $slugEntry = '$' . $this->data['parent']['slug'] . '_item_v[\'' . $this->data['slug'] . '_entry\']';
            $lines[] = $slugEntry . ' = isset(' . $slug . ') && trim(' . $slug . ') != \'\' ? $entity_Manager->find(\'Concrete\Core\Entity\Express\Entry\', ' . $slug . ') : null;';
        } else {
	        $lines[] = $this->getViewFunctionContentsExtra();
            $lines[] = '$this->set(\'' . $this->data['slug'] . '_entry\', trim($this->' . $this->data['slug'] . ') != \'\' ? $entity_Manager->find(\'Concrete\Core\Entity\Express\Entry\', $this->' . $this->data['slug'] . ') : null);';
        }
        return implode(PHP_EOL, $lines);
    }

    public function getSaveFunctionContents()
    {
        if ($this->getRepeating()) {
            $return = 'if (isset($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\']) && trim($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\']) != \'\') {
                    $data[\'' . $this->data['slug'] . '\'] = trim($' . $this->data['parent']['slug'] . '_item[\'' . $this->data['slug'] . '\']);
                } else {
                    $data[\'' . $this->data['slug'] . '\'] = null;
                }';
            return $return;
        }
    }

    public function getFormContents()
    {
        $repeating = $this->getRepeating();
        $btFieldsRequired = $repeating ? '$btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']' : '$btFieldsRequired';
        $html = '<div class="form-group">
    ' . parent::generateFormContent('label', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => $this->data['label'], 'description' => $this->data['description']], $repeating) . '
    ' . parent::generateFormContent('required', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'array' => $btFieldsRequired], $repeating) . '
    ' . parent::generateFormContent('express_entry_selector', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'attributes' => ['class' => 'express-entry-selector']], $repeating) . '
</div>';
        if ($repeating) {
            $html = PHP_EOL . '                    <?php if (is_object($' . $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '_entity\'])) { ?>
                        ' . $html . '
                    <?php } ?>' . PHP_EOL;
        }
        return $html;
    }

    public function getRepeatableUpdateItemJS()
    {
        if ($this->data['ft_count_repeatable'] <= 0) {
            return '$.each($(newField).find(\'.express-entry-selector\'), function () {
            $(this).concreteExpressEntrySelector({
                "entityID": $(this).attr(\'data-express-entity-id\'),
                "inputName": $(this).attr(\'data-express-input-name\'),
                "exEntryID": parseFloat($(this).attr(\'data-express-entry-id\'))
            });
        });';
        }
    }

    public function getFieldOptions()
    {
        return parent::view('field_options.php', 'block_designer_pro');
    }

    public function getAddEditFunctionContents()
    {
        $lines = [];
        if ($this->data['ft_count'] <= 0 && $this->data['ft_count_repeatable'] <= 0) {
            $lines[] = '$entity_Manager = Core::make(\'database/orm\')->entityManager();';
            $lines[] = '$this->set(\'entity_Manager\', $entity_Manager);';
        }
        if ($this->getRepeating()) {
            $lines[] = '$' . $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '_entity\'] = $entity_Manager->find(\'Concrete\Core\Entity\Express\Entity\', Database::connection()->fetchColumn(\'SELECT `id` FROM ExpressEntities WHERE `handle` = ?\', [\'' . $this->data['entity_handle'] . '\']));';
        } else {
            $lines[] = '$this->set(\'' . $this->data['slug'] . '_entity\', $entity_Manager->find(\'Concrete\Core\Entity\Express\Entity\', Database::connection()->fetchColumn(\'SELECT `id` FROM ExpressEntities WHERE `handle` = ?\', [\'' . $this->data['entity_handle'] . '\'])));';
        }
        return implode(PHP_EOL . '        ', $lines);
    }

    public function getDbFields()
    {
	    return [
		    [
			    'name' => $this->data['slug'],
			    'type' => $this->getDbType(),
		    ],
	    ];
    }

	public function getAssets()
	{
		return [
			'addEdit' => [
				'require' => [
					[
						'handle' => 'core/express',
					],
				],
			],
		];
	}

    public function getValidateFunctionContents()
    {
        $repeating = $this->getRepeating();
        $slug = $repeating ? '$' . $this->data['parent']['slug'] . '_v[\'' . $this->data['slug'] . '\']' : '$args["' . $this->data['slug'] . '"]';
        if ($repeating) {
            $btFieldsRequired = '$this->btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']';
            return 'if ((in_array("' . $this->data['slug'] . '", ' . $btFieldsRequired . ') || (isset(' . $slug . ') && trim(' . $slug . ') != \'\')) && (trim(' . $slug . ') == "" || ((!is_object(Core::make(\'database/orm\')->entityManager()->find(\'Concrete\Core\Entity\Express\Entry\', ' . $slug . ')))))) {
                            $e->add(t("The %s field is required (%s, row #%s).", t("' . h($this->data['label']) . '"), t("' . h($this->data['parent']['label']) . '"), $' . $this->data['parent']['slug'] . '_k));
                        }';
        } else {
            return 'if (in_array("' . $this->data['slug'] . '", $this->btFieldsRequired) && (trim(' . $slug . ') == "" || ' . $slug . ' == "" || ((!is_object(Core::make(\'database/orm\')->entityManager()->find(\'Concrete\Core\Entity\Express\Entry\', ' . $slug . ')))))) {
            $e->add(t("The %s field is required.", t("' . h($this->data['label']) . '")));
        }';
        }
    }
}