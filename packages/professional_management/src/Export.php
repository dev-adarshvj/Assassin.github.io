<?php namespace Concrete\Package\ProfessionalManagement\Src;
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
use Symfony\Component\HttpFoundation\StreamedResponse;
use UserAttributeKey;
use UserInfo;
use Concrete\Core\User\Group\Group;use Concrete\Core\User\Group\GroupList;
use UserList;
use League\Csv\Writer;
use Loader;
use core;
defined('C5_EXECUTE') or die(_("Access Denied."));
class Export {
    private static $fundfields= array(
        "employee_id" => array("name"=>"Employee ID","type"=>""),
        "entry_date"=>array("name"=>"Entry Date","type"=>"date"),
        "school"=>array("name"=>"School","type"=>""),
        "event_date"=>array("name"=>"Event Date","type"=>"date"),
        "cost"=>array("name"=>"Amount","type"=>""),
        "description"=>array("name"=>"Description","type"=>""),
        "ttoc_used"=>array("name"=>"Supplemental TTOC Used","type"=>""),
        "category"=>array("name"=>"Category","type"=>""),
        "year"=>array("name"=>"Year","type"=>""),
    );
    private static  $teacher_fields= array(
        "employee_id" => array("name"=>"Employee ID"),
        "name" => array("name"=>"Name"),
        "phone"=>array("name"=>"Phone"),
        "email"=>array("name"=>"Email"),
        "fte_2018"=>array("name"=>"FTE 2018"),
        "fte_2019"=>array("name"=>"FTE 2019"),
        "fte_2020"=>array("name"=>"FTE 2020"),
        "notes"=>array("name"=>"Notes")
    );
    public static function getFundFields(){
        return self::$fundfields;
    }
    public static function getTeacherFields(){
        return self::$teacher_fields;
    }
    public function downloadData($status){
        if($status == 'fundData'){ $this->downloadFundData(); } elseif ($status == 'teacherData'){ $this->downloadTeacherData(); die;}
    }
    public function downloadFundData(){
        $csv = array();
        $firstLine= array();
        $fields =  Export::getFundFields();
        $firstLine[] = 'App ID';
        foreach($fields  as $key  => $field)
        {
            $firstLine[]=$field["name"];
        }
        array_splice( $firstLine,2,0, array('Name','FTE 2018','FTE 2019','FTE 2020'));
        $csv[] = $firstLine;
        $db=Loader::db();
        $expances = $db->GetArray('select * from ProDFunds');
        foreach( $expances as $key => $expance)
        {
            $tLine= array();
            $tLine[] = $expance['id'];
            foreach($fields as $key  => $field)
            {
                if($field['type']=='date')
                {
                    $tLine[]=  date('d-M-y', strtotime($expance[$key]));
                } else {
                    $tLine[]=$expance[$key];
                }

            }
            $teacher = $this->getTeacherData($expance['employee_id']);
            array_splice( $tLine,2,0, array((string)$teacher['name'],$teacher['fte_2018'],$teacher['fte_2019'],$teacher['fte_2020']));
            $csv[] = $tLine;

        }
        $file = "ndta_pro-d_export_".date("m-d-Y").".csv";
        $this->fileExport($file,$csv);
    }
    public function downloadTeacherData(){
        $csv = array();
        $firstLine= array();
        $fields =  Export::getTeacherFields();
        foreach($fields  as $key  => $field)
        {
           $firstLine[]=$field["name"];;
        }
        $csv[] = $firstLine;
        $Teacher_group = \Concrete\Core\User\Group\Group::getByName('Teachers');
        $teacher = new UserList();
        $teacher->ignorePermissions();
        $teacher->filterByGroup($Teacher_group);
        $teacher->sortBy('ak_teacher_name', 'asc');
        $teachers = $teacher->getResults();
        foreach ($teachers as $key => $teacher) {
            $tLine= array();
            $tLine[] = $teacher_reg_no = $teacher->getUserName();
            $tLine[] = $teacher_name = $teacher->getAttribute('teacher_name');
            $tLine[] = $phone_number = $teacher->getAttribute('phone_number');
            $tLine[] = $Email = $teacher->getUserEmail();
            $tLine[] = $fte_2018 = $teacher->getAttribute('fte_2018');
            $tLine[] = $fte_2019 = $teacher->getAttribute('fte_2019');
            $tLine[] = $fte_2020 = $teacher->getAttribute('fte_2020');
            $tLine[] = $notes = $teacher->getAttribute('notes');
            $csv[] = $tLine;
            $tLine[] = array();
        }
        $file = "ndta_teacher_export_".date("m-d-Y").".csv";
        $this->fileExport($file,$csv);
      }
    private function fileExport($file,$csv){
        $filepath = '/home/naditeas/public_html/packages/professional_management/export_file/'.$file;
        $fp = fopen($filepath, 'w');
        foreach($csv as $line) { fputcsv($fp,$line); }
        fclose($fp);
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$file");
        header('Content-Type: text/csv; charset=utf-8');
        readfile($filepath);
        array_map( 'unlink', array_filter((array) glob('/home/naditeas/public_html/packages/professional_management/export_file/*') ) );
    }
    private function getTeacherData($teacher_id){
        $id = trim(explode(':',UserInfo::getByName($teacher_id))[1]);
        $teacher = UserInfo::getByID($id);
        if($teacher){
            $teacher_name = ($teacher->getAttribute('teacher_name')?(string)$teacher->getAttribute('teacher_name'):'');
            $fte_2018 = round($teacher->getAttribute('fte_2018'),2);
            $fte_2019 = round($teacher->getAttribute('fte_2019'),2);
            $fte_2020 = round($teacher->getAttribute('fte_2020'),2);
            $teacher_data = array(
                "name"=>$teacher_name,
                "fte_2018" => $fte_2018,
                "fte_2019" => $fte_2019,
                "fte_2020" => $fte_2020,
            );
            return $teacher_data;
        }
    }
}