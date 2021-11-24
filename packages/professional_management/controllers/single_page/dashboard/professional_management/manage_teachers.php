<?php namespace Concrete\Package\ProfessionalManagement\Controller\SinglePage\Dashboard\ProfessionalManagement;
use Concrete\Controller\Element\Search\Users\Header;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Csv\Export\UserExporter;
use Concrete\Core\Csv\WriterFactory;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\User\EditResponse as UserEditResponse;
use Concrete\Core\User\User;
use Concrete\Core\Workflow\Progress\UserProgress as UserWorkflowProgress;
use Exception;
use Imagine\Image\Box;
use PermissionKey;
use Permissions;
use stdClass;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UserAttributeKey;
use UserInfo;
use Concrete\Core\User\Group\Group;use Concrete\Core\User\Group\GroupList;
use Loader;
use UserList;
use League\Csv\Writer;
use Concrete\Package\ProfessionalManagement\Src\Export;
class ManageTeachers extends DashboardPageController {
    public $helpers = array('html', 'form');
    public function view(){
        $this->requireAsset('teacher');
        $Teacher_group = \Concrete\Core\User\Group\Group::getByName('Teachers');
        $teacher = new UserList();
        $teacher->ignorePermissions();
        $teacher->filterByGroup($Teacher_group);
        $teacher->sortBy('ak_teacher_name', 'asc');
        $teachers = $teacher->getResults();

        /* Adding popup teacher form */
        $locales = Localization::getAvailableInterfaceLanguageDescriptions();
        $attribs = UserAttributeKey::getRegistrationList();
        $assignment = PermissionKey::getByHandle('edit_user_properties')->getMyAssignment();
        $gl = new GroupList();
        $gArray = $gl->getPagination()->setMaxPerPage(10000)->getCurrentPageResults();

        $this->set('form', $this->app->make('helper/form'));
        $this->set('valt', $this->app->make('helper/validation/token'));
        $this->set('valc', $this->app->make('helper/concrete/validation'));
        $this->set('ih', $this->app->make('helper/concrete/ui'));
        $this->set('av', $this->app->make('helper/concrete/avatar'));
        $this->set('dtt', $this->app->make('helper/form/date_time'));
        $this->set('gArray', $gArray);
        $this->set('assignment', $assignment);
        $this->set('locales', $locales);
        $this->set('attribs', $attribs);
        /* end of popup Adding teacher form */
       	$this->set('teachers', $teachers);	}
    public function submit()
    {

        $assignment = PermissionKey::getByHandle('edit_user_properties')->getMyAssignment();

        $username = trim($_POST['uName']);
        $username = preg_replace("/\s+/", ' ', $username);
        $_POST['uName'] = $username;

        $password = $_POST['uPassword'];

        $this->app->make('validator/user/name')->isValid($username, $this->error);

        $this->app->make('validator/password')->isValid($password, $this->error);

        $this->app->make('validator/user/email')->isValid($_POST['uEmail'], $this->error);

        if (!$this->token->validate('submit')) {
            $this->error->add($this->token->getErrorMessage());
        }

        $aks = UserAttributeKey::getRegistrationList();

        foreach ($aks as $uak) {
            $controller = $uak->getController();
            $validator = $controller->getValidator();
            $response = $validator->validateSaveValueRequest(
                $controller, $this->request, $uak->isAttributeKeyRequiredOnRegister()
            );
            if (!$response->isValid()) {
                $error = $response->getErrorObject();
                $this->error->add($error);
            }
        }

        if (!$this->error->has()) {
            $data = ['uName' => $username, 'uPassword' => $password, 'uEmail' => $_POST['uEmail'], 'uDefaultLanguage' => $_POST['uDefaultLanguage']];
            $uo = UserInfo::add($data);
            if (is_object($uo)) {
                if ($assignment->allowEditAvatar()) {
                    if (!empty($_FILES['uAvatar']) && is_uploaded_file($_FILES['uAvatar']['tmp_name'])) {
                        $image = \Image::open($_FILES['uAvatar']['tmp_name']);
                        $image = $image->thumbnail(new Box(
                            Config::get('concrete.icons.user_avatar.width'),
                            Config::get('concrete.icons.user_avatar.height')
                        ));
                        $uo->updateUserAvatar($image);
                    }
                }

                $saveAttributes = [];
                foreach ($aks as $uak) {
                    if (in_array($uak->getAttributeKeyID(), $assignment->getAttributesAllowedArray())) {
                        $saveAttributes[] = $uak;
                    }
                }

                if (count($saveAttributes) > 0) {
                    $uo->saveUserAttributesForm($saveAttributes);
                }

                $gIDs = [];
                if (!empty($_POST['gID']) && is_array($_POST['gID'])) {
                    foreach ($_POST['gID'] as $gID) {
                        $gx = Group::getByID($gID);
                        $gxp = new Permissions($gx);
                        if ($gxp->canAssignGroup()) {
                            $gIDs[] = $gID;
                        }
                    }
                }

                $uo->updateGroups($gIDs);
                $uID = $uo->getUserID();
                $this->redirect('/dashboard/professional_management/manage_teachers', 'view');
            } else {
                $this->error->add(t('An error occurred while trying to create the account.'));
                $this->set('error', $this->error);
            }
        } else {
            $this->view();
        }
    }
    public function view_all_data(){
        $Teacher_group = \Concrete\Core\User\Group\Group::getByName('Teachers');
        $teacher = new UserList();
        $teacher->ignorePermissions();
        $teacher->filterByGroup($Teacher_group);
        $teacher->sortBy('ak_teacher_name', 'asc');
        $teachers = $teacher->getResults();
        $html ="";
        $html .= '<tr><th width="100"><label for="section" class="control-label">ID</label> </th>
                            <th width="360"><label for="keyword" class="control-label">Name</label></th>
                            <th width="300"><label for="keyword" class="control-label">Email</label></th>
                            <th width="50"><label for="keyword" class="control-label">Edit</label></th>
                            <th width="100"><label for="keyword" class="control-label">Summary</label></th>
                            <th width="50"><label for="keyword" class="control-label">Delete</label></th>
                            <th width="50"><label for="keyword" class="control-label">Email</label></th>
                        </tr>';
        foreach ($teachers as $member){
            $html .= '<tr>';
            $html .= '<td>'. $member->getUserName() .'</td>';
            $html .= '<td>'. $member->getAttributeValue('teacher_name') .'</td>';
            $html .= '<td>'. $member->getUserEmail() .'</td>';
            $html .= '<td><a class="btn btn-primary edit_teacher" onclick="addRecord('. $member->getUserID() .');">'. t('Edit') .'</a></td>';
            $html .= '<td class="btn btn-warning teacher_summary" onclick="viewSummary('. $member->getUserName() .');">'. t('Summary') .'</td>';
            $html .= '<td><a data-teacher-id="'. $member->getUserID() .'" class="btn btn-danger delete_teacher">'. t('Delete') .'</a></td>';
            $html .= '<td class="teacher_all_mail"><a class="btn btn-info teacher_mail" onclick="confirmEmail('. $member->getUserID() .','. $member->getUserName() .',\'single_mail\');">'. t('Email') .'</a></td>';
            $html .= '</tr>';
        }
        $response = new \Stdclass;
        $response->res_html = $html;
        echo json_encode($response);
        exit();
    }
    public function delete_teacher(){
        if(!empty($_POST)) {
            $uID = $_POST['id'];
            $db=Loader::db();
            if($uID > 0){
                $this->setupUser($uID);
                if ($this->canDeleteUser) {
                    $this->user->triggerDelete($this->user);
                   exit();
                }
            }
        }
    }
    protected function setupUser($uID)
    {
        $me = new User();
        $ui = UserInfo::getByID($this->app->make('helper/security')->sanitizeInt($uID));
        if (is_object($ui)) {
            $up = new Permissions($ui);
            if (!$up->canViewUser()) {
                throw new Exception(t('Access Denied.'));
            }
            $tp = new Permissions();
            $pke = PermissionKey::getByHandle('edit_user_properties');
            $this->user = $ui;
            $this->assignment = $pke->getMyAssignment();
            $this->canEdit = $up->canEditUser();
            $this->canActivateUser = $this->canEdit && $tp->canActivateUser() && $me->getUserID() != $ui->getUserID();
            $this->canEditAvatar = $this->canEdit && $this->assignment->allowEditAvatar();
            $this->canEditUserName = $this->canEdit && $this->assignment->allowEditUserName();
            $this->canEditLanguage = $this->canEdit && $this->assignment->allowEditDefaultLanguage();
            $this->canEditTimezone = $this->canEdit && $this->assignment->allowEditTimezone();
            $this->canEditEmail = $this->canEdit && $this->assignment->allowEditEmail();
            $this->canEditPassword = $this->canEdit && $this->assignment->allowEditPassword();
            $this->canSignInAsUser = $this->canEdit && $tp->canSudo() && $me->getUserID() != $ui->getUserID();
            $this->canDeleteUser = $this->canEdit && $tp->canDeleteUser() && $me->getUserID() != $ui->getUserID();
            $this->canAddGroup = $this->canEdit && $tp->canAccessGroupSearch();
            $this->allowedEditAttributes = [];
            if ($this->canEdit) {
                $this->allowedEditAttributes = $this->assignment->getAttributesAllowedArray();
            }
            $this->set('user', $ui);
            $this->set('canDeleteUser', $this->canDeleteUser);
        }
    }
    public function export_fund(){
        $export = new Export();
        $export->downloadData('fundData');
    }
    public function export_teacher(){
        $export = new Export();
        $export->downloadData('teacherData');
    }
}