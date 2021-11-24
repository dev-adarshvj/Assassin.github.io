<?php
namespace Concrete\Package\TeamManagement\Controller\SinglePage\Dashboard\TeamManagement;
use \Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;
use \Concrete\Core\Page\Controller\DashboardPageController;
use AttributeSet;
use Loader;
use Config;
use PageTemplate;
use PageType;


class Settings extends DashboardPageController {

  public function view(){
	  
	  //get all attribute set 
	  $category=AttributeKeyCategory::getByID(1);
	  $sets = $category->getAttributeSets();
	  $setsarr=array();
	  foreach($sets as $set){
		$setsarr[$set->getAttributeSetID()]=$set->getAttributeSetName();
		}
	$this->set('attribute_sets', $setsarr);
	  //get all page template 
	$ctArray = PageTemplate::getList();
		$PageTemplates = array(''=>'Select Page Template');
		foreach($ctArray as $ct) {
			$PageTemplates[$ct->getPageTemplateID()] = $ct->getPageTemplateName();
		}
	$this->set('PageTemplates', $PageTemplates);
	 //get all page type 
		$ctArray = PageType::getList();
		$PageTypes = array(''=>'Select Page Type');
		foreach($ctArray as $ct) {
			$PageTypes[$ct->getPageTypeID()] = $ct->getPageTypeName();
		}
	//set variables	
	$this->set('PageTypes', $PageTypes);
	$this->set('attribute_set_id',Config::get('concrete.team_management_attribute_set_id'));
	$this->set('page_type_id',Config::get('concrete.team_management_page_type_id'));
	$this->set('page_template_id',Config::get('concrete.team_management_page_template_id'));
	

  }
  public function save_settings() {

		//save data via concrete5 config
		if ($this->token->validate("save_settings")) {
		if ($this->isPost()) {
		if(isset($_POST['TEAM_MANAGEMENT_ATTRIBUTE_SET_ID'])){
			Config::save('concrete.team_management_attribute_set_id', $_POST['TEAM_MANAGEMENT_ATTRIBUTE_SET_ID']);
		}
		if(isset($_POST['TEAM_MANAGEMENT_PAGE_TYPE_ID'])){
			Config::save('concrete.team_management_page_type_id', $_POST['TEAM_MANAGEMENT_PAGE_TYPE_ID']);
		}
		if(isset($_POST['TEAM_MANAGEMENT_PAGE_TEMPLATE_ID'])){
			Config::save('concrete.team_management_page_template_id', $_POST['TEAM_MANAGEMENT_PAGE_TEMPLATE_ID']);
		}
		


		$this->set('message', t('Settings has been saved.')); //set save success message
		$this->view();
		}
		} else {
		$this->set('error', array($this->token->getErrorMessage()));//set save error message
		}
	}



	}
