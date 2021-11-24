<?php namespace Concrete\Package\BlockDesigner\Controller\SinglePage\Dashboard\Blocks;

defined('C5_EXECUTE') or die("Access Denied.");

use AssetList;
use BlockTypeList;
use Concrete\Core\Page\Controller\DashboardPageController;
use Core;
use Database;

class BlockOrder extends DashboardPageController
{
    public $helpers = ['form'];
    public $packageHandle = 'block_designer';

    private function getBlockTypeSets()
    {
        $db = Database::connection();
        $blockTypeSets = $db->fetchAll('SELECT * FROM BlockTypeSets ORDER BY btsDisplayOrder');
        $options = [];
        foreach ($blockTypeSets as $blockTypeSet) {
            $blockTypeSet['name'] = t($blockTypeSet['btsName']);
            $blockTypeSet['blocks'] = [];
            $options[$blockTypeSet['btsID']] = $blockTypeSet;
        }
        return $options;
    }

    private function getBlockTypes($btsID = false)
    {
        $db = Database::connection();
        $whereValues = ['0'];
        if (!$btsID) {
            $queryString = 'SELECT *, bt.btID FROM BlockTypes bt LEFT JOIN BlockTypeSetBlockTypes btsbt ON btsbt.btID = bt.btID WHERE btIsInternal = ? ORDER BY btDisplayOrder';
        } else {
            $whereValues[] = $btsID;
            $queryString = 'SELECT *, bt.btID FROM BlockTypes bt LEFT JOIN BlockTypeSetBlockTypes btsbt ON btsbt.btID = bt.btID WHERE btIsInternal = ? AND btsbt.btsID = ? ORDER BY btDisplayOrder';
        }
        $blockTypes = $db->fetchAll($queryString, $whereValues);
        $options = [];
        foreach ($blockTypes as $blockType) {
            $blockType['name'] = t($blockType['btName']);
            $options[$blockType['btID']] = $blockType;
        }
        return $options;
    }

    public function view()
    {
        $al = AssetList::getInstance();
        $al->register('css', 'block-designer-order-view', 'css/block_order.view.css', [], $this->packageHandle);
        $al->register('javascript', 'block-designer-order-view', 'js/block_order.view.js', [], $this->packageHandle);
        $this->requireAsset('css', 'block-designer-order-view');
        $this->requireAsset('javascript', 'block-designer-order-view');
        $blockTypeSets = $this->getBlockTypeSets();
        $blockTypeSets['other'] = [
	        'btsID'  => 'other',
	        'name'   => t("Other"),
	        'blocks' => [],
        ];
        if ($blockTypes = ($this->getBlockTypes())) {
            $btl = new BlockTypeList();
            $btInstalledArray = $btl->get();
            $ci = Core::make('helper/concrete/urls');
            foreach ($btInstalledArray as $k => $_bt) {
                $btIcon = $ci->getBlockTypeIconURL($_bt);
                $btID = $_bt->getBlockTypeID();
                if (isset($blockTypes[$btID])) {
                    $blockTypes[$btID]['icon'] = $btIcon;
                }
            }
            foreach ($blockTypes as $blockType) {
                if (isset($blockTypeSets[$blockType['btsID']])) {
                    $blockTypeSets[$blockType['btsID']]['blocks'][] = $blockType;
                } else {
                    $blockTypeSets['other']['blocks'][] = $blockType;
                }
            }
        }
        $this->set('blockTypeSets', $blockTypeSets);
    }

    public function update()
    {
        if (isset($_POST['btsID'], $_POST['order']) && is_array($_POST['order']) && !empty($_POST['order'])) {
            $db = Database::connection();
            if ($_POST['btsID'] == 'other') {
                $blockTypes = $this->getBlockTypes();
            } else {
                $blockTypes = $this->getBlockTypes($_POST['btsID']);
            }
            $i = 0;
            foreach ($_POST['order'] as $v) {
                if (isset($blockTypes[$v])) {
                    $db->executeQuery('UPDATE BlockTypes SET btDisplayOrder = ? WHERE btID = ?', [$i, $v]);
                    $i++;
                }
            }
        }
        exit;
    }
}