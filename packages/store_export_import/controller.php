<?php  
namespace Concrete\Package\StoreExportImport; 

use Package;
use SinglePage;
use Loader;  
use Config;  
use Page;
use Exception; 
use Route;
use Whoops\Exception\ErrorException;

defined('C5_EXECUTE') or die(_("Access Denied."));

class Controller extends Package {
	
	 protected $pkgHandle = 'store_export_import';
	 protected $appVersionRequired = '5.7.1';
	 protected $pkgVersion = '1.0.0';
	
	 public function getPackageDescription() {
	 	 return t("To export store orders, products, user details and import products.");
	 }
	
	public function getPackageName() {
	    return t("Community Store Export/Import");
	}
	
	public function uninstall(){
	parent::uninstall();
	}
	
	public function install() {
		
		
	 $installed = Package::getInstalledHandles();
        if(!(is_array($installed) && in_array('community_store',$installed)) ) {
            throw new ErrorException(t('This package requires that Community Store be installed'));
        } else {
            $pkg = parent::install();
			$this->install_dp_singlepages($pkg);
        }	
		
	
	}
	
	function install_dp_singlepages($pkg){
		if(Page::getByPath("/dashboard/store_export_import")){
		SinglePage::add("/dashboard/store_export_import", $pkg);
		}
	}
	
	public function registerRoutes()
    {
        //Route::register('/product/save', '\Concrete\Package\CommunityStore\Controller\SinglePage\Dashboard\Store\Products::Save');
    }
	
    public function on_start()
    {
        $this->registerRoutes();
		
	}
	
	
}