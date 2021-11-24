<?php namespace Concrete\Package\BlockDesigner\Controller\SinglePage\Dashboard\Blocks\BlockDesigner;

defined('C5_EXECUTE') or die("Access Denied.");

use AssetList;
use Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use RamonLeenders\BlockDesigner\BlockDesignerProcessor;
use Request;

class Settings extends DashboardPageController
{
	protected $pkgHandle = 'block_designer';

	public function sortArrayByArray(array $array, array $orderArray)
	{
		$ordered = [];
		foreach ($orderArray as $key) {
			if (array_key_exists($key, $array)) {
				$ordered[$key] = $array[$key];
				unset($array[$key]);
			}
		}
		return $ordered + $array;
	}

	public function view($saved = null)
	{
		$al = AssetList::getInstance();
		$al->register('css', 'block_designer.settings', 'css/block_designer.settings.css', [], $this->pkgHandle);
		$al->register('javascript', 'block_designer.settings', 'js/block_designer.settings.js', [], $this->pkgHandle);
		$this->requireAsset('css', 'block_designer.settings');
		$this->requireAsset('javascript', 'block_designer.settings');
		$this->requireAsset('css', 'select2');
		$this->requireAsset('javascript', 'select2');

		$ftHideOptions = [];
		$ftOrder = Config::get($this->pkgHandle . '.ft_order');
		$ftOrderOptions = [];
		$fieldTypes = BlockDesignerProcessor::getFieldTypes();
		foreach ($fieldTypes as $k => $v) {
			$ftOrderOptions[$k] = $v;
			$ftHideOptions[$k] = $v['name'];
		}
		$ftOrderOptions = $this->sortArrayByArray($ftOrderOptions, is_array($ftOrder) ? $ftOrder : []);
		$post = Request::post();
		$ftSortOptions = [
			'asort'  => t('Field Type Name (ascending)'),
			'arsort' => t('Field Type Name (descending)'),

			'ksort'  => t('Field Type Handle (ascending)'),
			'krsort' => t('Field Type Handle (descending)'),

			//'natsort'     => t('Field Type Name "natural" (descending)'),
			//'natcasesort' => t('Field Type Name "natural" case insensitive (descending)'),

			'usort' => t('Custom'),
		];
		if ($saved == 'saved') {
			$this->set('message', t("Settings saved."));
		}
		if (!empty($post)) {
			if ($this->token->validate("block_designer_settings")) {
				Config::save($this->pkgHandle . '.ft_sort', isset($post['ft_sort']) && array_key_exists($post['ft_sort'], $ftSortOptions) ? $post['ft_sort'] : 'asort');
				Config::save($this->pkgHandle . '.ft_order', isset($post['ft_order']) && is_array($post['ft_order']) ? $post['ft_order'] : []);
				Config::save($this->pkgHandle . '.ft_hide', isset($post['ft_hide']) && is_array($post['ft_hide']) ? $post['ft_hide'] : []);
				$this->redirect('/dashboard/blocks/' . $this->pkgHandle . '/settings/saved');
			} else {
				$this->set('error', [$this->token->getErrorMessage()]);
			}
		}
		$this->set('ft_sort', Config::get($this->pkgHandle . '.ft_sort'));
		$this->set('ft_hide', Config::get($this->pkgHandle . '.ft_hide'));
		$this->set('ft_order', $ftOrder);
		$this->set('ftSortOptions', $ftSortOptions);
		$this->set('ftOrderOptions', $ftOrderOptions);
		$this->set('ftHideOptions', $ftHideOptions);
		$this->set('form', $this->app->make('helper/form'));
	}
}