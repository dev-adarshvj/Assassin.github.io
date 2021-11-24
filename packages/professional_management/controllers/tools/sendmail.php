<?php namespace Concrete\Package\ProfessionalManagement\Controller\Tools;
use Concrete\Package\ProfessionalManagement\Src\MailDatas;
use config;
class sendmail {
    public function send_mail(){
        $send_mail = new MailDatas();
        if($_POST['teacher_id']>0 && $_POST['status'] == 'single_mail'){
            $teacher_id = $_POST['teacher_id'];
            $teacher_reg_no = $_POST['teacher_reg_no'];
            $status = $_POST['status'];
            $send_mail->getPDF($teacher_id,$teacher_reg_no,$status);
        }
    }
} ?>