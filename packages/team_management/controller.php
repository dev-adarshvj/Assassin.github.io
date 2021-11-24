<?php

namespace Concrete\Package\TeamManagement;

use Concrete\Core\Application\Application;
use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Asset\AssetList;
use Concrete\Core\Asset\Asset;
use AttributeSet;
use Package;
use BlockType;
use SinglePage;
use Core;
use Route;
use Loader;  
use Config;  
use PageTemplate;
use Page;
use Exception; 
use PageType;
use \Concrete\Core\Page\Type\PublishTarget\Type\Type as PublishTargetType;

defined('C5_EXECUTE') or die(_("Access Denied."));

class Controller extends Package
{

    protected $pkgHandle = 'team_management';
    protected $appVersionRequired = '5.8.0';
    protected $pkgVersion = '0.0.0';

    public function getPackageDescription()
    {
        return t("To Manage teams");
    }

    public function getPackageName()
    {
        return t("Team Management");
    }

    public function uninstall()
    {

        parent::uninstall();
    }
	
    public function install()
    {
        $pkg = parent::install();
        $this->installContentFile('install.xml'); //install page type and attributes via xml
		$this->install_dp_pages($pkg);
    }

    public function upgrade()
    {
        $pkg = parent::upgrade();
        $this->installContentFile('update.xml');//updata page type and attributes via xml
		$this->install_dp_pages($pkg);
    }

	
	function install_dp_pages($pkg) {
	
	//page templates
    $NewsPageTypes = array('pTemplateHandle' => 'team_management_details',   'pTemplateName' => t('Team Details'),'pTemplateIcon'=>t('left_sidebar.png'));
	$pagetype = PageTemplate::getByHandle($NewsPageTypes['ctHandle']);
	if (!is_object($pagetype)) {
    PageTemplate::add($NewsPageTypes['pTemplateHandle'],$NewsPageTypes['pTemplateName'],$NewsPageTypes['pTemplateIcon'], $pkg);
	}
	
	$team_management_details = PageTemplate::getByHandle('team_management_details');
	
	$teamItem = PageType::getByHandle('team');
    if (!is_object($teamItem)) {
    $teamItem = PageType::add(
        array(
            'handle' => 'team',
            'name' => 'Team', 
            'defaultTemplate' => $team_management_details,
            'templates' => array($team_management_details), 
        ),
        $pkg
    );
    }
	}
}
