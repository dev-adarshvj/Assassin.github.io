<?php namespace Concrete\Package\ProfessionalManagement\Controller\SinglePage\Dashboard;
use \Concrete\Core\Page\Controller\DashboardPageController;
defined('C5_EXECUTE') or die(_("Access Denied."));
class ProfessionalManagement extends DashboardPageController {
    public function view() {
        $this->redirect('/dashboard/professional_management/manage_teachers');
    }
}