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
use stdClass;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UserAttributeKey;
use UserInfo;
use Concrete\Core\User\Group\Group;use Concrete\Core\User\Group\GroupList;
use UserList;
use League\Csv\Writer;
use Loader;
use core;

defined('C5_EXECUTE') or die(_("Access Denied."));
class SubmitDatas{
    private  $schools = array("Bayview"=>"Bayview","Brechin"=>"Brechin","Chase River"=>"Chase River","Cedar Elementary"=>"Cedar Elementary","Cedar Secondary"=>"Cedar Secondary","Cilaire"=>"Cilaire","Cinnabar"=>"Cinnabar","Coal Tyee"=>"Coal Tyee","Departure Bay"=>"Departure Bay","Dover Bay"=>"Dover Bay","Fairview"=>"Fairview","Forest Park"=>"Forest Park","Frank J. Ney"=>"Frank J. Ney","Gabriola"=>"Gabriola","Georgia Avenue"=>"Georgia Avenue","Hammond Bay"=>"Hammond Bay","Island Connected"=>"Island Connected","John Barsby"=>"John Barsby","Ladysmith Intermediate"=>"Ladysmith Intermediate","Ladysmith Primary"=>"Ladysmith Primary","Ladysmith Secondary"=>"Ladysmith Secondary","Learning Services"=>"Learning Services","McGirr"=>"McGirr","Mountainview"=>"Mountainview","NDSS"=>"NDSS","North Oyster"=>"North Oyster","Park Avenue"=>"Park Avenue","Pauline Haarer"=>"Pauline Haarer","Pleasant Valley"=>"Pleasant Valley","Quarterway"=>"Quarterway","Randerson Ridge"=>"Randerson Ridge","Rock City"=>"Rock City","Rutherford"=>"Rutherford","Seaview"=>"Seaview","Uplands Park"=>"Uplands Park","Wellington"=>"Wellington","DAC Annex"=>"DAC Annex");
    private  $ttoc = array(''=>'','0.5'=>'0.5','1'=>'1');
    private  $category = array("Other"=>"Other","Reserve for Whole School"=>"Reserve for Whole School","TTOC Costs"=>"TTOC Costs","Whole School"=>"Whole School");
    private  $year = array("2018-2019"=>"2018-2019","2019-2020"=>"2019-2020","2020-2021"=>"2020-2021","Remedy"=>"Remedy");
    private  $table = 'ProDFunds';
    public function addFund($proDdata){
     $db=Loader::db();
     $response = new \Stdclass;
    $entry_date = $proDdata[0]['value'];
    $school = $proDdata[1]['value'];
    $event_date = $proDdata[2]['value'];
    $cost = $proDdata[3]['value'];
    $description = $proDdata[4]['value'];
    $ttoc_used = $proDdata[5]['value'];
    $category = $proDdata[6]['value'];
    $year = $proDdata[7]['value'];
    $status = $proDdata[8]['value'];
    $employee_id = $proDdata[9]['employee_id'];
        $data = array(
            'employee_id' => $proDdata[9]['employee_id'],
            'entry_date' => $proDdata[0]['value'],
            'school' => $proDdata[1]['value'],
            'event_date' => $proDdata[2]['value'],
            'cost' => $proDdata[3]['value'],
            'description' => $proDdata[4]['value'],
            'category' => $proDdata[5]['value'],
            'ttoc_used' => $proDdata[6]['value'],
            'year' => $proDdata[7]['value'],
        );
    if($status == 'add'){
      $db->query("INSERT INTO ProDFunds(employee_id,entry_date,school,event_date,cost,description,category,ttoc_used,year) values(?,?,?,?,?,?,?,?,?)",array($employee_id,$entry_date,$school,$event_date,$cost,$description,$category,$ttoc_used,$year));
        $response->success = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>'. t("Successfully Added!").'</strong></div>';
    }elseif ($status == 'update'){
        $fund_id =  $proDdata[9]['fund_id'];
        if($fund_id>0){
            $db->update($this->table, $data, array('id' => $fund_id));
        $response->success = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>'. t("Successfully Updated!").'</strong></div>';
        }
    }
        echo json_encode($response);
        exit();
    }
    public function delete_fund($fundID){
        $db=Loader::db();
        $response = new \Stdclass;
        $db->Execute("DELETE FROM $this->table where id = $fundID");
        $response = '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>'. t("Successfully Deleted!").'</strong></div>';
        echo json_encode($response);
        exit();
    }
    public function getAllExpenses($empId,$id,$status){
        $db=Loader::db();
        $response = new \Stdclass;
        $fund_expenses = "";
        $fund_story = "";
        $fund_used = "";
        $fund_left = "";
        $fte_value_2018 = 400;
        $fte_value_2019 = 300;
        $fte_value_2020 = 300;
        $remedy_used = 0;
        $ui = UserInfo::getByID($id);
        $teacher_name = $ui->getAttribute('teacher_name');
        $fte_2018 = round($ui->getAttribute('fte_2018'),2);
        $fte_2019 = round($ui->getAttribute('fte_2019'),2);
        $fte_2020 = round($ui->getAttribute('fte_2020'),2);
        $remedy = $ui->getAttribute('remedy');
        $fund_remedy = "";
        $expense_data = $db->GetArray('select * from ProDFunds where employee_id = ?', array($empId));
       if(count($expense_data)>0){
          foreach($expense_data as $expense){
               if($expense['year'] == "Remedy") {
                   $remedy_used +=  $expense['cost'];
               } else {
                   $t='fte_'.$expense['year'];
                   $years= explode('-',$expense['year']);
                   ${'used'.$years[0]} +=  round($expense['cost'],2);
               }
           }
       }
           $total_used = $used2018+$used2019+$used2020;
           $total_left = $fte_2018*$fte_value_2018 +$fte_2019*$fte_value_2019+$fte_2020*$fte_value_2020 - $total_used;

           if($fte_2018 == 0) { $fte_2018 = 'N/A'; } else if ($fte_2018 == 1) { $fte_2018 = '1.0'; } else if ($fte_2018 < 1) { $fte_2018 = rtrim($fte_2018, '0'); }
           if($fte_2019 == 0) { $fte_2019 = 'N/A'; } else if ($fte_2019 == 1) { $fte_2019 = '1.0'; } else if ($fte_2019 < 1) { $fte_2019 = rtrim($fte_2019, '0'); }
           if($fte_2020 == 0) { $fte_2020 = 'N/A'; } else if ($fte_2020 == 1) { $fte_2020 = '1.0'; } else if ($fte_2020 < 1) { $fte_2020 = rtrim($fte_2020, '0'); }
           if($remedy == 0) { $remedy = 'N/A';
           } else {
               $remedy = $remedy;
               $remedy_remaining = $remedy - $remedy_used;
           }
           $fund_story .= '<div class="min-t f-right">';
           $fund_story .= '<span >18/19</span>    <span>19/20</span>    <span>20/21</span><br /> ';
           $fund_story .= '<span class="prod-value">'. $fte_2018 .'</span>';
           $fund_story .= '<span class="prod-value">'. $fte_2019 .'</span>';
           $fund_story .= '<span class="prod-value">'. $fte_2020 .'</span>';
           $fund_story .= '<br />';
           $fund_story .= '<span class="prod-value">'. (!is_null($fte_2018)?"$".number_format(($fte_2018*$fte_value_2018),2):"N/A") .'</span>';
           $fund_story .= '<span class="prod-value">'. (!is_null($fte_2019)?"$".number_format(($fte_2019*$fte_value_2019),2):"N/A") .'</span>';
           $fund_story .= '<span class="prod-value">'. (!is_null($fte_2020)?"$".number_format(($fte_2020*$fte_value_2020),2):"N/A") .'</span>';
           $fund_story .= '</div>';
           $fund_story .= '<span class="f-left"></span><br /><span class="f-left">FTE : </span><br /><span class="f-left text_13px" style="margin-top:10px;">Funds:</span><br />';

           $fund_used .= '<h2 class="text_18px">Funds Used</h2>';
           $fund_used .= '<p class="center ">Total Used:'. "$".number_format($total_used,2) .'</p>';
           $fund_used .= '<p>2018/2019:  <span class="prod-value">'. (!is_null($used2018)?'$'.number_format($used2018,2):('N/A')) .'</span></p>';
           $fund_used .= '<p>2019/2020: <span class="prod-value">'. (!is_null($used2019)?'$'.number_format($used2019,2):('N/A')) .'</span></p>';
           $fund_used .= '<p>2020/2021:  <span class="prod-value">'. (!is_null($used2020)?'$'.number_format($used2020,2):('N/A')) .'</span></p>';

           $fund_left .= '<h2 class="text_18px">Funds Remaining</h2>';
           $fund_left .= '<p class="center ">Total Remaining:'. '$'.number_format($total_left,2) .'</p>';
           $fund_left .= '<p>2018/2019: <span class="prod-value">'. (!is_null($fte_2018)?'$'.number_format(($fte_2018*$fte_value_2018- $used2018),2):'N/A') .'</span></p>';
           $fund_left .= '<p>2019/2020: <span class="prod-value">'. (!is_null($fte_2019)?'$'.number_format(($fte_2019*$fte_value_2019 - $used2019),2):'N/A') .'</span></p>';
           $fund_left .= '<p>2020/2021: <span class="prod-value">'. (!is_null($fte_2020)?'$'.number_format(($fte_2020*$fte_value_2020 - $used2020),2):'N/A') .'</span></p>';

if($remedy>0) {
$fund_remedy .= '<h2 class="text_18px">Remedy </h2>';
$fund_remedy .= '<p class="center ">Total Remedy:'. '$'.number_format($remedy,2) .'</p>';
$fund_remedy .= '<p class="center ">Used:'. '$'.number_format($remedy_used,2) .'</p>';
$fund_remedy .= '<p class="center ">Remaining:'. '$'.number_format($remedy_remaining,2) .'</p>';
}

$fund_expenses .= '<table id="expense-table" width="100%" cellpadding="6">';
$fund_expenses .= '<tr><th width="60">App ID</th><th width="85">Entry Date</th><th>School</th><th  width="75">Event Date</th><th>Description</th><th>Category</th><th>Amount</th>';
$fund_expenses .= '<th width="50">Supplemental TTOC Used</th><th>Year</th><th width="70">Edit</th><th width="70">Delete</th><th width="70">Duplicate</th></tr>';
        if(count($expense_data)>0){
foreach($expense_data as $expense){
    $fund_expenses .= '<tr><td>'. $expense['id'] .'</td>';
    $fund_expenses .= '<td>'. date('d-M-y', strtotime($expense['entry_date'])) .'</td>';
    $fund_expenses .= '<td>'. $expense['school'] .'</td>';
    $fund_expenses .= '<td>'. date('d-M-y', strtotime($expense['event_date'])) .'</td>';
	$fund_expenses .= '<td>'. $expense['description'] .'</td>';
    $fund_expenses .= '<td>'. $expense['category'] .'</td>';
    $fund_expenses .= '<td>'. '$'.number_format(round($expense['cost'],2),2) .'</td>';
    $fund_expenses .= '<td>'. ($expense['ttoc_used']?$expense['ttoc_used']:'') .'</td>';
	$fund_expenses .= '<td>'. $expense['year'] .'</td>';
	$fund_expenses .= '<td><a class="btn btn-primary" data-employ-name="'. $teacher_name .'" onclick="editproDform('.trim($expense['id']).', employ_name = \''. $teacher_name .'\',\'update\');">Edit</a></td>';
    $fund_expenses .= '<td><a class="btn btn-danger delete_teacher" onclick="confirmDeleteRecord('. $expense['id'] .')">Delete</a></td>';
	$fund_expenses .= '<td><a class="btn btn-info" onclick="editproDform('.trim($expense['id']).', employ_name = \''. $teacher_name .'\',\'add\')">Duplicate</a></td></tr>';
			} }
    $fund_expenses .= '	<tr><td colspan="12" height="20" bgcolor="#e2eaed" bordercolor="#666666"  ><a class="btn btn-success dashboard_btn pull-left" onclick="addproDfund('. $empId . ',' . $id .')">+ Add New</a></td></tr></table>';


           $response->fund_story = $fund_story;
           $response->fund_used = $fund_used;
           $response->fund_left = $fund_left;
           $response->fund_remedy = $fund_remedy;
           $response->fund_expenses = $fund_expenses;
           $response->proDform = $this->proD_form($empId,$teacher_name,$status);
        echo json_encode($response);
        exit();
    }
    public function Show_proD_edit_form($data){
        $response = new \Stdclass;
        $response = $this->proD_edit_form($data);
        echo json_encode($response);
        exit();
    }
    public function proD_form($Id,$employ_name,$status){
        $db=Loader::db();
        $form = Loader::helper('form');
        $editor = Core::make('editor');

        $proDform = "";
        if($status == "add"){ $proDform .= '<div data-dialog-wrapper="add-proDfund" class="ccm-ui ui-dialog-content ui-widget-content">';}
        $proDform .= '<div id="ccm-block-fields">';
        $proDform .= '<h1>'. $employ_name .'</h1>';
        $proDform .= '<form method="post" action="" class="proDdata" data-employ-id="'. $Id .'" >';
        $proDform .= '<fieldset>';
        $proDform .= '<div class="form-group"><label for="entry_date" class="control-label">'. t('Entry Date') .'</label>';
        $proDform .= '<div class="input-group"><input type="date" name="entry_date" class="form-control ccm-input-date" value="'. date('Y-m-d') .'" /></div></div>';
        $proDform .= '<div class="form-group"><label for="school" class="control-label">'. t('School') .'</label>';
        $proDform .= '<div class="input-group">';
        $proDform .= $form->select('school', $this->schools,array('class'=>' ccm-input-select'));
        $proDform .= '</div></div>';
        $proDform .= '<div class="form-group"><label for="event_date" class="control-label">'. t('Event Date') .'</label>';
        $proDform .= '<div class="input-group"><input type="date" name="event_date" class="form-control ccm-input-date" value="'. date('Y-m-d') .'" /></div></div>';
        $proDform .= '<div class="form-group"><label for="cost" class="control-label">'. t('Amount') .'</label>';
        $proDform .= '<div class="input-group">'. $form->text('cost', array('autofocus' => 'autofocus', 'autocomplete' => 'off'));
        $proDform .= '<span class="input-group-addon"><i class="fa fa-asterisk"></i></span></div></div>';
        $proDform .= '<div class="form-group"><label for="description" class="control-label">'. t('Description') .'</label>';
        $proDform .= '<div class="input-group">'. $editor->outputStandardEditor('description', '');
        $proDform .= '</div></div>';
        $proDform .= '</fieldset><fieldset>';
        $proDform .= '<div class="form-group"><label for="ttoc_used" class="control-label">'. t('Supplemental TTOC Used') .'</label>';
        $proDform .= '<div class="input-group">';
        $proDform .= $form->select('ttoc_used', $this->ttoc,array('class'=>' ccm-input-select'));
        $proDform .= '</div></div>';
        $proDform .= '<div class="form-group"><label for="category" class="control-label">'. t('Category') .'</label>';
        $proDform .= '<div class="input-group">';
        $proDform .= $form->select('category', $this->category,array('class'=>' ccm-input-select'));
        $proDform .= '</div></div>';
        $proDform .= '<div class="form-group"><label for="year" class="control-label">'. t('Year') .'</label>';
        $proDform .= '<div class="input-group">';
        $proDform .= $form->select('year', $this->year,array('class'=>' ccm-input-select'));
        $proDform .= '</div></div>';
        $proDform .= '</fieldset>';
        $proDform .= $form->hidden('status', $status);
        $proDform .= '<fieldset><div class="ccm-dashboard-form-actions-wrapper"><div class="ccm-dashboard-form-actions">';
        $proDform .= '<a href="/dashboard/professional_management/manage_teachers" class="btn btn-default pull-left form_cancel">'. t('Cancel') .'</a>';
        $proDform .=  Loader::helper("form")->submit('add', t('Save Entry'), array('class' => 'btn btn-primary pull-right save_proDfund'));
        $proDform .=  '</div></div></fieldset>';
        $proDform .=  '</form></div>';
        if($status == "add"){ $proDform .= '</div>';}
        $proDform .=  '<script>';
        $proDform .=  '$( ".save_proDfund" ).click(function(e) {';
        $proDform .=  'e.preventDefault();';
        $proDform .=  'var fData = $(\'.proDdata\').serializeArray();';
        $proDform .=  'fData.push({employee_id: '.$Id.'});';
       $proDform .=  'console.log(fData);';
        $proDform .=  '$.ajax({
            url: \'/add_proDfund\',
            type: \'POST\',
            data: { fundData : fData },           
            success: function (response) {
            $( "button.ui-dialog-titlebar-close" ).trigger( "click" );    
              response = JSON.parse(response);                            
              $(\'.fund_msg_box\').html(response.success);
              $( ".update-employ").trigger(\'click\');
             $(".fund_msg_box").show().delay(5000).fadeOut();   
            },
            error: function () {
                alert("error");
            }
        });';
        $proDform .=  '});';
        $proDform .= '$( "a.form_cancel" ).click(function(e) { e.preventDefault(); closePopup();});';
        $proDform .= 'function closePopup() { $( "button.ui-dialog-titlebar-close" ).trigger( "click" ); }';
        $proDform .= '</script>';
        return $proDform;
    }

    public function proD_edit_form($data){
            $fund_id = $data['proDfundID'];
            $employ_name = $data['employ_name'];
            $status = $data['status'];
            $db=Loader::db();
            $expense_data = $db->GetRow('select * from ProDFunds where id = ?', array($fund_id));
            $employ_id = $expense_data['employee_id'];
            $school = $expense_data['school'];
            $cost = $expense_data['cost'];
            $description = $expense_data['description'];
            $ttoc_used = $expense_data['ttoc_used'];
            $year = $expense_data['year'];
            $category = $expense_data['category'];
        $entry_date = $expense_data['entry_date'] ? $expense_data['entry_date'] : date('Y-m-d');
        $event_date = $expense_data['event_date'] ? $expense_data['event_date'] : date('Y-m-d');

          $form = Loader::helper('form');
          $editor = Core::make('editor');

          $proDform = "";
          $proDform .= '<div id="ccm-block-fields">';
          $proDform .= '<h1>'. $employ_name .'</h1>';
          $proDform .= '<form method="post" action="" class="proD_Editdata" data-fund-id="'. $fund_id .'" >';
          $proDform .= '<fieldset>';
          $proDform .= '<div class="form-group"><label for="entry_date" class="control-label">'. t('Entry Date') .'</label>';
          $proDform .= '<div class="input-group"><input type="date" name="entry_date" class="form-control ccm-input-date" value="'.  $entry_date .'" /></div></div>';
          $proDform .= '<div class="form-group"><label for="school" class="control-label">'. t('School') .'</label>';
          $proDform .= '<div class="input-group">';
          $proDform .= $form->select('school', $this->schools,$expense_data['school'],array('class'=>' ccm-input-select'));
          $proDform .= '</div></div>';
          $proDform .= '<div class="form-group"><label for="event_date" class="control-label">'. t('Event Date') .'</label>';
          $proDform .= '<div class="input-group"><input type="date" name="event_date" class="form-control ccm-input-date" value="'. $event_date .'" /></div></div>';
          $proDform .= '<div class="form-group"><label for="cost" class="control-label">'. t('Amount') .'</label>';
          $proDform .= '<div class="input-group">'. $form->text('cost',$expense_data['cost'], array('autofocus' => 'autofocus', 'autocomplete' => 'off'));
          $proDform .= '<span class="input-group-addon"><i class="fa fa-asterisk"></i></span></div></div>';
          $proDform .= '<div class="form-group"><label for="description" class="control-label">'. t('Description') .'</label>';
          $proDform .= '<div class="input-group">'. $editor->outputStandardEditor('description', $expense_data['description'] ? $expense_data['description'] : '');
          $proDform .= '</div></div>';
          $proDform .= '</fieldset><fieldset>';
          $proDform .= '<div class="form-group"><label for="ttoc_used" class="control-label">'. t('Supplemental TTOC Used') .'</label>';
          $proDform .= '<div class="input-group">';
          $proDform .= $form->select('ttoc_used', $this->ttoc,$expense_data['ttoc_used'],array('class'=>' ccm-input-select'));
          $proDform .= '</div></div>';
          $proDform .= '<div class="form-group"><label for="category" class="control-label">'. t('Category') .'</label>';
          $proDform .= '<div class="input-group">';
          $proDform .= $form->select('category', $this->category,$expense_data['category'],array('class'=>' ccm-input-select'));
          $proDform .= '</div></div>';
          $proDform .= '<div class="form-group"><label for="year" class="control-label">'. t('Year') .'</label>';
          $proDform .= '<div class="input-group">';
          $proDform .= $form->select('year', $this->year,$expense_data['year'],array('class'=>' ccm-input-select'));
          $proDform .= '</div></div>';
          $proDform .= '</fieldset>';
          $proDform .= $form->hidden('status', $status);
          $proDform .= '<fieldset><div class="ccm-dashboard-form-actions-wrapper"><div class="ccm-dashboard-form-actions">';
          $proDform .= '<a href="/dashboard/professional_management/manage_teachers" class="btn btn-default pull-left form_cancel">'. t('Cancel') .'</a>';
          $proDform .=  Loader::helper("form")->submit('add', t('Save Entry'), array('class' => 'btn btn-primary pull-right update_proDfund'));
          $proDform .=  '</div></div></fieldset>';
          $proDform .=  '</form></div>';
         $proDform .=  '<script>';
         $proDform .=  '$( ".update_proDfund" ).click(function(e) {';
         $proDform .=  'e.preventDefault();';
         $proDform .=  'var fData = $(\'.proD_Editdata\').serializeArray();';
         $proDform .=  'fData.push({employee_id: '.$employ_id.',fund_id: '.$fund_id.'});';
         $proDform .=  'console.log(fData);';
         $proDform .=  '$.ajax({
             url: \'/add_proDfund\',
             type: \'POST\',
             data: { fundData : fData },
             success: function (response) {
              $( "button.ui-dialog-titlebar-close" ).trigger( "click" );    
              response = JSON.parse(response);                            
              $(\'.fund_msg_box\').html(response.success);
              $( ".update-employ").trigger(\'click\');
             $(".fund_msg_box").show().delay(5000).fadeOut();                        
             },
             error: function () {
                 alert("error");
             }
         });';
        $proDform .=  '});';
        $proDform .= '$( "a.form_cancel" ).click(function(e) { e.preventDefault(); closePopup();});';
        $proDform .= 'function closePopup() { $( "button.ui-dialog-titlebar-close" ).trigger( "click" );}';
        $proDform .= '</script>';
        return $proDform;
    }
}