<?php namespace Concrete\Package\Prime;
use Route;
use Package;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Package\Prime\Src\Page\Theme\GridFramework\Type\Bootstrap4 as Bootstrap4GridFramework;
defined('C5_EXECUTE') or die(_("Access Denied."));

class Controller extends Package {
    protected $pkgHandle = 'prime';
    protected $appVersionRequired = '5.8.0';
    protected $pkgVersion = '0.0.0';
    protected $pkgDescription = "Barton, Walter & Krier P.C.";
    protected $pkgName = "Prime:BWK";

    public function install()
    {
        parent::install();
        $this->installContentFile('install.xml');
    }

    public function upgrade()
    {
        parent::upgrade();
        $this->installContentFile('update.xml');
    }

    public function on_start()
    {
      $app = Facade::getFacadeApplication();
      $manager = $app->make('manager/grid_framework');
      $manager->extend('bootstrap4', function ($app) {
          return new Bootstrap4GridFramework();
      });
// $this->registerRoutes();
    }
    /*public function registerRoutes()
       {
   	Route::register('loadmore', '\Concrete\Package\Prime\Block\ServicesHighlight\Controller::loadmore');
       }*/


}
