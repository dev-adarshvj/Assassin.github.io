<?php namespace Concrete\Package\ProfessionalManagement;
use Package;
use Page;
use SinglePage;
use BlockType;
use Loader;
use Route;
use AssetList;
use Asset;
defined('C5_EXECUTE') or die(_("Access Denied."));
class Controller extends Package{
    protected $pkgHandle = 'professional_management';
    protected $appVersionRequired = '5.8';
    protected $pkgVersion = '0.0.5';
    public function getPackageName()	{		return t("Professional Management");	}
    public function getPackageDescription()	{		return t("Professional Management");	}
    public function uninstall()	{		parent::uninstall();	}
    public function install()	{
        $pkg = parent::install();
        $this->install_dp_singlepages($pkg);
    }
    function install_dp_singlepages($pkg)	{
        if(Page::getByPath("/dashboard/professional_management"))		{			SinglePage::add("/dashboard/professional_management", $pkg);		}
        if(Page::getByPath("/dashboard/professional_management/manage_teachers"))		{	SinglePage::add("/dashboard/professional_management/manage_teachers", $pkg);		}
    }
    public function on_start()    {
        $al = AssetList::getInstance();
        $al->register('css', 'teacher', 'css/teacher.css', array('version' => '1', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => false, 'combine' => false), $this);
        $al->registerGroup('teacher', array(
            array('css', 'teacher'),
        ));
        Route::register('/employ_details','\Concrete\Package\ProfessionalManagement\Controller\Tools\Prodfunds::employDetails');
        Route::register('/add_proDfund','\Concrete\Package\ProfessionalManagement\Controller\Tools\Prodfunds::add_proDfund');
        Route::register('/edit_prodForm','\Concrete\Package\ProfessionalManagement\Controller\Tools\Prodfunds::edit_proDfund_form');
        Route::register('/delete_fund','\Concrete\Package\ProfessionalManagement\Controller\Tools\Prodfunds::delete_fund');
        Route::register('/send_mail','\Concrete\Package\ProfessionalManagement\Controller\Tools\sendmail::send_mail');
    }}