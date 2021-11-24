<?php
namespace Concrete\Package\TeamManagement\Controller\SinglePage\Dashboard\TeamManagement;
use \Concrete\Core\Page\Controller\DashboardPageController;
use \Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;
use CollectionAttributeKey;
use Loader;
use Block;
use Config;
use PageList;
use BlockType;
use TaskPermission;
use PageTemplate;
use PageType;
use Page;
use Permissions;
use AttributeSet;
use UserInfo;
use URL;

defined('C5_EXECUTE') or die(_("Access Denied."));
class AddEdit extends DashboardPageController {
	public $num = 15;

	public $helpers = array('html','form');

	public function on_start() {
		//Loader::model('page_list');
		$this->error = Loader::helper('validation/error');
		$this->set('pageTitle','Add a Team');
	}

	public function view() {
		//view pages
		$this->setupForm();//load function
		$this->loadTeamManagementSections();//load function
		$pageList = new PageList();
		//sort by order
		if($_GET['ccm_order_by'] && $_GET['ccm_order_dir']){
			$pageList->sortBy('ak_'.$_GET['ccm_order_by'], $_GET['ccm_order_dir']);
		}else{
			$pageList->sortBy('cDateAdded', 'desc');
		}
		//search/filter by parent ID
		if (isset($_GET['cParentID']) && $_GET['cParentID'] > 0) {
			$pageList->filterByParentID($_GET['cParentID']);
		} else {
			$sections = $this->get('sections');
			$keys = array_keys($sections);
			$keys[] = -1;
			$pageList->filterByParentID($keys);
		}
		$this->set('pageList', $pageList);
		$this->set('pageResults', $pageList->getResults());
	}

	protected function loadTeamManagementSections() {
		//filter by selected team attribute section
		$pageSectionList = new PageList();
		$pageSectionList->filterByTeamManagementSection(1);// filter by team attribute set
		$pageSectionList->sortBy('cvName', 'desc');
		$tmpSections = $pageSectionList->get();
		$sections = array();
		$sections = array(''=>'Select a Section');
		foreach($tmpSections as $_c) {
			if($_c->getCollectionAttributeValue('team_management_section')){
				$sections[$_c->getCollectionID()] = $_c->getCollectionName();
			}
		}
		$this->set('sections', $sections);
	}
	public function edit($cID) {
		$this->set('pageTitle','Edit Team');
		$this->setupForm();
		$page = Page::getByID($cID);//set edit page OBJ
		$sections = $this->get('sections');
		if (in_array($page->getCollectionParentID(), array_keys($sections))) {
			$this->set('page', $page);
		} else {
			$this->redirect('/dashboard/team_management/');
		}
	}

	public function delete($cID) {
		$this->setupForm();
		$page = Page::getByID($cID);//set delete page OBJ
		$sections = $this->get('sections');
		if (in_array($page->getCollectionParentID(), array_keys($sections))) {
			$this->set('page', $page);
		} else {
			$this->redirect('/dashboard/team_management/team_management_list');
		}
	}

	protected function setupForm() {
		//filter by selected team attribute section
		$this->loadTeamManagementSections();
		$ctArray = PageTemplate::getList();
		$PageTemplates = array();
		foreach($ctArray as $ct) {
			if($ct->getPageTemplateName()!='Home'){
			$PageTemplates[$ct->getPageTemplateID()] = $ct->getPageTemplateName();
			}
		}
		$this->set('PageTemplates', $PageTemplates);

	}

	public function add() {
		//add pages
		$this->setupForm();
		if ($this->isPost()) {
			$this->validate();
			if (!$this->error->has()) {
				$parent = Page::getByID($this->post('cParentID'));//get page OBJ
				$ct = PageType::getByID($this->post('ctID'));//get PageType OBJ
				$pt = PageTemplate::getByID($this->post('ptID'));//get Page Template OBJ
				$data = array('uID' =>$this->post('uID'),'cName' => $this->post('page_title'), 'cDescription' => $this->post('pageDescription'), 'cDatePublic' => Loader::helper('form/date_time')->translate('page_date_time'));
				$p = $parent->add($ct, $data,$pt);
					$this->saveData($p);
				$this->redirect('/dashboard/team_management/team_management_list', 'team_added');// Redirect page to team list if saving success
				 }
		}
	}

	public function update() {
		$this->edit($this->post('pageID'));
		//update pages
		if ($this->isPost()) {
			$this->validate();
			if (!$this->error->has()) {
				$p = Page::getByID($this->post('pageID'));//get page OBJ
				$parent = Page::getByID($this->post('cParentID'));//get PageType OBJ
				$pt = PageTemplate::getByID($this->post('ptID'));//get Page Template OBJ
                $handle = str_replace(' ', '-', strtolower($this->post('page_title')));
				$data = array('uID' =>$this->post('uID'),'pTemplateID' =>$this->post('ptID'), 'cDescription' => $this->post('pageDescription'), 'cName' => $this->post('page_title'),'cHandle' => $handle,'cDatePublic' => Loader::helper('form/date_time')->translate('page_date_time'));
				$p->update($data);
				$p->rescanCollectionPath();
				if ($p->getCollectionParentID() != $parent->getCollectionID()) {
					$p->move($parent);
				}
				$this->saveData($p);
				$this->redirect('/dashboard/team_management/team_management_list', 'team_updated');// Redirect page to team list if saving success

				 }
		}
	}

protected function validate() {
		//validate fields and attributes
		$vt = Loader::helper('validation/strings');
		$vn = Loader::Helper('validation/numbers');
		$dt = Loader::helper("form/date_time");
		//set error array
		if (!$vn->integer($this->post('cParentID'))) {
			$this->error->add(t('You must choose a parent page for this Page entry.'));
		}

		if (!$vn->integer($this->post('ctID'))) {
			$this->error->add(t('You must choose a page Template from settings for this Page entry.'));
		}
		if (!$vn->integer($this->post('ptID'))) {
			$this->error->add(t('You must choose a page type   from settings for this Page entry.'));
		}

		if (!$vt->notempty($this->post('page_title'))) {
			$this->error->add(t('Title is required'));
		}

		if (!$this->error->has()) {
			$parent = Page::getByID($this->post('cParentID'));
			$parentPermissions = new Permissions($parent);
			if (!$parentPermissions->canAddSubCollection($ct)) {
				$this->error->add(t('You do not have permission to add a page of that type to that area of the site.'));
			}
		}
	}

	private function saveData($p) {

	//save attributes
	$attributeset_id=Config::get('concrete.team_management_attribute_set_id');//get config values
    $set = AttributeSet::getByID($attributeset_id);//get attribute objects
	if(is_object($set)){
		$setAttribs = $set->getAttributeKeys();
		if($setAttribs){
			foreach ($setAttribs as $ak) {
				$aksv = CollectionAttributeKey::getByHandle($ak->getAttributeKeyHandle());
        $controller = $aksv->getController();
        $value = $controller->createAttributeValueFromRequest();
        $p->setAttribute($aksv, $value);
			}
		}
	}

	}
      public function on_before_render() {
		$this->set('error', $this->error);
	}


	}
