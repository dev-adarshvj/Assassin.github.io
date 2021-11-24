<?php namespace Concrete\Package\BlockDesigner;

use Package;
use Page;
use SinglePage;
use Symfony\Component\ClassLoader\Psr4ClassLoader as SymfonyClassLoader;

defined('C5_EXECUTE') or die("Access Denied.");

class Controller extends Package
{
	protected $pkgHandle = 'block_designer';
	protected $appVersionRequired = '5.7.0.4';
	protected $pkgVersion = '2.9.1';

	public function getPackageName()
	{
		return t("Block Designer");
	}

	public function getPackageDescription()
	{
		return t("Design your own content blocks within a few clicks!");
	}

	public function install()
	{
		$this->on_start();
		$package = parent::install();
		$this->installUpgrade($package);
	}

	public function upgrade()
	{
		$this->on_start();
		$this->installUpgrade($this);
		parent::upgrade();
	}

	public function on_start()
	{
		$strictLoader = new SymfonyClassLoader();
		$strictLoader->addPrefix('\RamonLeenders\BlockDesigner', DIR_PACKAGES . '/block_designer/src');
		$strictLoader->register();
	}

	protected function installSinglePages($package)
	{
		$singlePages = [
			['path' => '/dashboard/blocks/block_order'],
			['path' => sprintf('/dashboard/blocks/%s', $this->pkgHandle)],
			['path' => sprintf('/dashboard/blocks/%s/block_config', $this->pkgHandle)],
			['path' => sprintf('/dashboard/blocks/%s/settings', $this->pkgHandle)],
		];
		foreach ($singlePages as $singlePage) {
			$singlePageObject = Page::getByPath($singlePage['path']);
			// Check if it exists, if not, add it
			if ($singlePageObject->isError() || (!is_object($singlePageObject))) {
				$sp = SinglePage::add($singlePage['path'], $package);
				unset($singlePage['path']);
				if (!empty($singlePage)) {
					// And make sure we update the page with the remaining values
					$sp->update($singlePage);
				}
			}
		}
	}

	protected function installUpgrade($package)
	{
		$this->installSinglePages($package);
	}

	public function getToolbarDesignerPresets()
	{
		return [
			'/dashboard/blocks/block_designer' => [
				'title'             => t('Block Designer'),
				'controller_handle' => 'item_default',
				'pkg_handle'        => 'toolbar_designer',
				'cPath'             => '/dashboard/blocks/block_designer',
				'icon'              => 'fa-square',
			],
		];
	}
}