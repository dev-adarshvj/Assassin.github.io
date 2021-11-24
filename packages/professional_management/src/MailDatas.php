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
class MailDatas {
    public function getPDF($teacher_id,$teacher_reg_no,$status){
        $db=Loader::db();
        $html = '';
        $fte_value = "400";
        $ui = UserInfo::getByID($teacher_id);
        $teacher_name = $ui->getAttribute('teacher_name');
        $fte_2018 = round($ui->getAttribute('fte_2018'),2);
        $fte_2019 = round($ui->getAttribute('fte_2019'),2);
        $fte_2020 = round($ui->getAttribute('fte_2020'),2);
        $ttoc_used_2018 = round($ui->getAttribute('ttoc_used_2018'),2);
        $ttoc_used_2019 = round($ui->getAttribute('ttoc_used_2019'),2);
        $ttoc_used_2020 = round($ui->getAttribute('ttoc_used_2020'),2);
        $remedy = $ui->getAttribute('remedy');
        $employ_mail = $ui->getUserEmail();
        $fund_remedy = "";
        $expense_data = $db->GetArray('select * from ProDFunds where employee_id = ?', array($teacher_reg_no));
       if(count($expense_data)>0){
          foreach($expense_data as $expense){
              $t='fte_'.$expense['year'];
              $years= explode('-',$expense['year']);
              ${'used'.$years[0]} += round($expense['cost'],2);
           }

       }
        $total_used = $used2018+$used2019+$used2020;
        $total_left = $fte_2018*$fte_value + $fte_2019*$fte_value + $fte_2020*$fte_value - $total_used;


           if($fte_2018 == 0) { $fte_2018 = 'N/A'; } else if ($fte_2018 == 1) { $fte_2018 = '1.0'; } else if ($fte_2018 < 1) { $fte_2018 = rtrim($fte_2018, '0'); }
           if($fte_2019 == 0) { $fte_2019 = 'N/A'; } else if ($fte_2019 == 1) { $fte_2019 = '1.0'; } else if ($fte_2019 < 1) { $fte_2019 = rtrim($fte_2019, '0'); }
           if($fte_2020 == 0) { $fte_2020 = 'N/A'; } else if ($fte_2020 == 1) { $fte_2020 = '1.0'; } else if ($fte_2020 < 1) { $fte_2020 = rtrim($fte_2020, '0'); }
        $html .= '<style>'.file_get_contents('/home/naditeas/public_html/packages/professional_management/css/pdf.css').'</style>';
        $html .= '<div id="prod-wrapper" style="color:#339fc6; font-family:Arial, Helvetica, sans-serif;">';
        $html .= '<h1 >Teacher Pro-D Summary</h1>';
        $html .= '<table id="t-info" width="100%" cellpadding="4"><tr>';
        $html .= '<td width="40%">';
        $html .= '<div  class=" f-left info-column">';
        $html .= '<p>Employee #:'.$teacher_id.'</p><p>Name : '.$teacher_name.'</p><br/>';
        $html .= '<table class="min-t f-right">';
        $html .= '<tr><td></td><td><span >'.t("18/19").'</span></td><td><span>'.t("19/20").'</span></td><td><span>'.t("20/21").'</span></td></tr>';
        $html .= '<tr><td><span class="f-left">FTE : </span><br /></td><td><span>'.$fte_2018.'</span></td><td><span>'.$fte_2019.'</span></td><td><span>'.$fte_2020.'</span></td></tr><br />';
        $html .= '<tr><td><span class="f-left" style="width:100px;">Pro-D Funds : </span><br /></td>';
        $html .= '<td><span>' . ($fte_2018 != 0 ?("$".number_format($fte_2018*$fte_value,2)):("N/A")) .'</span></td>';
    $html .= '<td><span>'. ($fte_2019 != 0 ?('$'.number_format($fte_2019*$fte_value,2)):('N/A')) .'</span></td>';
    $html .= '<td><span>'. ($fte_2020 != 0 ?('$'.number_format($fte_2020*$fte_value,2)):('N/A')) .'</span></td></tr>';
	$html .= '</table>';
    $html .= '</div></td>';
    $html .= '<td width="30%">';
    $html .= '<div id="funds-used" class=" f-left  info-column">';
        $html .= '<h2 class="text_18px">Funds Used</h2>';
        $html .= '<span class="center block ">Total Used: ' . (!is_null($total_used)?('$'.number_format($total_used,2)):('N/A')) . '</span>';
        $html .= '<p >Used - 2018/2019:  ' . ($used2018 != 0 ?('$'.number_format($used2018,2)):('N/A')) .'</p>';
        $html .= '<p>Used - 2019/2020: ' . ($used2019 != 0 ?('$'.number_format($used2019,2)):('N/A')) .'</p>';
        $html .= '<p>Used - 2020/2018: ' . ($used2020 != 0 ?('$'.number_format($used2020,2)):('N/A')) .'</p></div></td>';
        $html .= ' <td width="30%">';
        $html .= '<div id="funds-left" class=" f-left  info-column">';
        $html .= '<h2 class="text_18px">Funds Remaining</h2>';
        $html .= '<span class="center block">Total Remaining: '. (!is_null($total_left)?('$'.number_format($total_left,2)):('N/A')) .'</span>';
        $html .= '<p>Remaining - 2018/2019: '. ($fte_2018 != 0 ?('$'.number_format(($fte_2018*$fte_value- $used2018),2)):('N/A')) .'</p>';
        $html .= '<p>Remaining - 2019/2020: '. ($fte_2019 != 0 ?('$'.number_format(($fte_2019*$fte_value- $used2019),2)):('N/A')).'</p>';
        $html .= '<p>Remaining - 2020/2021: '. ($fte_2020 != 0 ?('$'.number_format(($fte_2020*$fte_value- $used2020),2)):('N/A')) .'</p>';
        $html .= '</div></td></tr></table>';
        $html .= '<div id="expense-table"><table id="expense-table"  width="100%" cellpadding="6">';
        $first_head = 'style="background-color: #cedee4;color: #1a90ba;"';
        $second_head = 'style="background-color: #e2eaed;color: #1a90ba;"';
        $child_td = 'style="background-color: #e3eef2;"';
        $html .= '<tr style="border-bottom: 1px solid #becbd0;"><th  '.$first_head.'>App ID</th><th '.$second_head.'>Entry Date</th><th  '.$first_head.'>School</th><th '.$second_head.'>Event Date</th><th  '.$first_head.'>Description</th><th '.$second_head.'>Category</th><th  '.$first_head.'>Amount</th><th '.$second_head.'>Supplemental TTOC Used</th><th  '.$first_head.'>Year</th></tr>';
        if(count($expense_data)>0){
        foreach($expense_data as $expense){
$html .= '<tr><td '.$child_td.'>'.$expense['id'].'</td><td>'.date('d-M-y', strtotime($expense['entry_date'])).'</td><td '.$child_td.'>'.$expense['school'].'</td><td>'.date('d-M-y',strtotime($expense['event_date'])).'</td><td '.$child_td.'>'.$expense['description'].'</td><td>'.$expense['category'].'</td><td '.$child_td.'>$'.number_format(round($expense['cost'],2),2).'</td><td>'.$expense['ttoc_used'].'</td><td '.$child_td.'>'.$expense['year'].'</td></tr>';
        }}
        $html .= '</table></div></div>';
        //echo $html;
   Loader::packageElement('printpdf/pdf_template', 'professional_management', array('teacher_name' => $teacher_name, 'employID' => $teacher_reg_no, 'status'=>$status, 'employMail'=>$employ_mail, 'pdfcontent' => $html));
  exit();
      }
}