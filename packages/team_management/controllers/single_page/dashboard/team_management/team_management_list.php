<?php
namespace Concrete\Package\TeamManagement\Controller\SinglePage\Dashboard\TeamManagement;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Loader;
use Page;
use PageList;
use Core;
use PageTemplate;


defined('C5_EXECUTE') or die(_("Access Denied."));
class TeamManagementList extends DashboardPageController {

	public $itemsPerPage=1;
	public $num = 1;

	public $helpers = array('html','form');

	public function on_start() {
		$this->error = Loader::helper('validation/error');
		$this->set('pageTitle','Team List');//set page title
	}


	public function view() {

		$this->loadTeamManagementSections();
		$pageList = new PageList();
		$itemsperpage=10;
		//echo $pageList->getSortByURL('cvName', 'asc');
		//die;
		/*STATUS FILTER*/
		if($_GET['ccm_order_dir']=='asc'){
			$ccm_order_dir='desc';
		}else{
			$ccm_order_dir='asc';
		}

		$this->set('ccm_order_dir',$ccm_order_dir);
		/*STATUS FILTER*/
		if($_GET['ccm_order_by'] && $_GET['ccm_order_dir']){
			$pageList->sortBy('ak_'.$_GET['ccm_order_by'], $_GET['ccm_order_dir']);

		}else{
			$pageList->sortBy('cDateAdded', 'desc');
			//$pageList->sortBy('cID', 'asc');
		}
		//filter by parent ID
		if (isset($_GET['cParentID']) && $_GET['cParentID'] > 0) {
			$pageList->filterByParentID($_GET['cParentID']);
		} else {
			$sections = $this->get('sections');
			$keys = array_keys($sections);
			$keys[] = -1;
			$pageList->filterByParentID($keys);
		}
		//filter by keyword
		if(!empty($_GET['like'])){
			$pageList->filterByName($_GET['like']);
			}
		//	print_r($pageList);
		/*	CATEGORY FILTER */
		if($_GET['ccm_order_dir_cat']=='asc'){
			$ccm_order_dir_cat='desc';
			$pageList->sortBy('pp.cPath', 'asc');
		}elseif($_GET['ccm_order_dir_cat']=='desc'){
			$ccm_order_dir_cat='asc';
			$pageList->sortBy('pp.cPath', 'desc');
			}
			$this->set('ccm_order_dir_cat',$ccm_order_dir_cat);
		/*	CATEGORY FILTER */
		/*	NAME FILTER*/
		if($_GET['ccm_order_dir_name']=='asc'){
			$ccm_order_dir_name='desc';
			$pageList->sortBy($_GET['ccm_order_by_name'], $_GET['ccm_order_dir_name']);
		}elseif($_GET['ccm_order_dir_name']=='desc'){
			$ccm_order_dir_name='asc';
			$pageList->sortBy($_GET['ccm_order_by_name'], $_GET['ccm_order_dir_name']);
			}

		$this->set('ccm_order_dir_name',$ccm_order_dir_name);
		/*NAME*/
		/*DATE	FILTER*/
		if($_GET['ccm_order_dir_date']=='asc'){
			$ccm_order_dir_date='desc';
			$pageList->sortBy($_GET['ccm_order_by_date'], $_GET['ccm_order_dir_date']);
		}elseif($_GET['ccm_order_dir_date']=='desc'){
			$ccm_order_dir_date='asc';
			$pageList->sortBy($_GET['ccm_order_by_date'], $_GET['ccm_order_dir_date']);
			}
		$this->set('ccm_order_dir_date',$ccm_order_dir_date);
		/*DATE	*/
		if ($_GET['numResults']>0) {
		$pageList->setItemsPerPage($_GET['numResults']);
		$numResults=$_GET['numResults'];
		}else{
		$pageList->setItemsPerPage($itemsperpage);
		$numResults=$itemsperpage;
		}
		 $totalPages=count($pageList->getResults());

 		 $showPagination = false;
		 //get pagination
		 $pagination = $pageList->getPagination();
		 $pages = $pagination->getCurrentPageResults();
		 $paginationList = $pagination->renderDefaultView();
		 //print_r($_REQUEST['ccm_paging_p']);
		 // die;
         if($totalPages > $numResults || $_REQUEST['ccm_paging_p'] ){
		 $showPagination = true;
		 $this->set('pagination', $paginationList);
		 }

		 if ($showPagination) {
            $this->requireAsset('css', 'core/frontend/pagination');
        }

        $this->set('pageList', $pages);
        $this->set('showPagination', $showPagination);
	}
	protected function loadTeamManagementSections() {

		//filter by selected team attribute section
		$pageSectionList = new PageList();
		//$pageSectionList->setItemsPerPage(2);
		$pageSectionList->filterByAttribute('team_management_section',1);
		//$pageSectionList->sortBy('cvName', 'desc');
		$tmpSections = $pageSectionList->get();
		$sections = array();
		foreach($tmpSections as $_c) {
			if($_c->getCollectionAttributeValue('team_management_section')){
				$sections[$_c->getCollectionID()] = $_c->getCollectionName();
			}
		}

		$this->set('sections', $sections);
	}
	public function delete_check($cIDd,$name) {
		$this->set('remove_name',$name);
		$this->set('remove_cid',$cIDd);
		$this->view();
	}

	public function delete($cIDd,$name) {
		$c= Page::getByID($cIDd);//get page object
		$db = Loader::db();
		$c->delete();
		$this->set('message', t('"'.$name.'" has been deleted'));
		$this->set('remove_name','');
		$this->set('remove_cid','');
		$this->view();
	}

	public function duplicate($cIDd,$name){
		$c = Page::getByID($cIDd);//get page object
		$cpID = $c->getCollectionParentID();
		$cp = Page::getByID($cpID);//get page object
		$c->duplicate($cp);
		$this->set('message', t('"'.$name.'" has been duplicated'));
		$this->view();
	}

	public function clear_warning(){
		$this->set('remove_name','');
		$this->set('remove_cid','');
		$this->view();
	}

	protected function validate() {
		$vt = Loader::helper('validation/strings');
		$vn = Loader::Helper('validation/numbers');
		$dt = Loader::helper("form/date_time");
		//set error array
		if (!$vn->integer($this->post('cParentID'))) {
			$this->error->add(t('You must choose a parent page for this Page entry.'));
		}

		if (!$vn->integer($this->post('ctID'))) {
			$this->error->add(t('You must choose a page type for this Page entry.'));
		}

		if (!$vt->notempty($this->post('page_title'))) {
			$this->error->add(t('Title is required'));
		}

		if (!$this->error->has()) {
			Loader::model('collection_types');
			$ct = CollectionType::getByID($this->post('ctID'));
			$parent = Page::getByID($this->post('cParentID'));
			$parentPermissions = new Permissions($parent);
			if (!$parentPermissions->canAddSubCollection($ct)) {
				$this->error->add(t('You do not have permission to add a page of that type to that area of the site.'));
			}
		}
	}

	private function saveData($p) {
		Loader::model("attribute/categories/collection");
		
		//attribute save
		$set = AttributeSet::getByHandle('page');
		$setAttribs = $set->getAttributeKeys();
		if($setAttribs){
			foreach ($setAttribs as $ak) {
				$aksv = CollectionAttributeKey::getByHandle($ak->akHandle);
				$controller = $aksv->getController();
        $value = $controller->createAttributeValueFromRequest();
        $p->setAttribute($aksv, $value);
			}
		}
	}

	public function team_added() {
		$this->set('message', t('Team added.'));
		$this->view();
	}

	public function team_updated() {
		$this->set('message', t('Team updated.'));
		$this->view();
	}

	public function team_deleted() {
		$this->set('message', t('Team deleted.'));
		$this->view();
	}

	public function on_before_render() {
		$this->set('error', $this->error);
	}

}
