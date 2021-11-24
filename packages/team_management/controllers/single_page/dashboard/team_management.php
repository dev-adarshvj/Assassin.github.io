<?php
namespace Concrete\Package\TeamManagement\Controller\SinglePage\Dashboard;
use \Concrete\Core\Page\Controller\DashboardPageController;

defined('C5_EXECUTE') or die(_("Access Denied."));

class TeamManagement extends DashboardPageController {
	/**
	* Dashboard view - automatically redirects to a default
	* page in the category
	*
	* @return void
	*/
	public function view() {
		$this->redirect('/dashboard/team_management/team_management_list');// redirect to team list
	}
}
