<?php namespace Concrete\Package\BlockDesignerPro;

use Package;
use Exception;
use Symfony\Component\ClassLoader\Psr4ClassLoader as SymfonyClassLoader;

defined('C5_EXECUTE') or die("Access Denied.");

class Controller extends Package
{
    protected $pkgHandle = 'block_designer_pro';
    protected $appVersionRequired = '5.7.0.4';
    protected $pkgVersion = '2.8.5';

    public function getPackageName()
    {
        return t('Block Designer Pro');
    }

    public function getPackageDescription()
    {
        return t("Extra field types/features on top of the 'Block Designer' Add-On");
    }

    public function install()
    {
        $this->on_start();
        $blockDesignerPkg = Package::getByHandle('block_designer');
        if (!is_object($blockDesignerPkg)) {
            throw new Exception(t("Installation requires as a prerequisite that <a href='%s' target='_blank'>Block Designer</a> is already installed.", 'http://www.concrete5.org/marketplace/addons/block-designer'));
        } else {
            parent::install();
        }
    }

    public function on_start()
    {
        $strictLoader = new SymfonyClassLoader();
        $strictLoader->addPrefix('\RamonLeenders\BlockDesignerPro', DIR_PACKAGES . '/block_designer_pro/src');
        $strictLoader->register();
    }

    protected function fieldTypes(){
        return [
	        t("row"),
        ];
    }
}