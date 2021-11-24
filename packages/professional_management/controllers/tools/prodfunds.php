<?php namespace Concrete\Package\ProfessionalManagement\Controller\Tools;
use Concrete\Package\ProfessionalManagement\Src\SubmitDatas;
use config;
class Prodfunds {
    public function employDetails(){
        if($_POST['employ_id']>0 && $_POST['id']>0){
            $employ_id = $_POST['employ_id'];
            $id = $_POST['id'];
            $status = $_POST['status'];
            $employ_detail = new SubmitDatas();
            $employ_detail->getAllExpenses($employ_id,$id,$status);
        }
    }
    public function add_proDfund(){
        if($_POST['fundData']){
            $data = $_POST['fundData'];
            extract($data);
            $proD_fund = new SubmitDatas();
            $proD_fund->addFund($data);
        }
    }
    public function edit_proDfund_form(){
            if($_POST){
                $data = $_POST;
                $getForm = new SubmitDatas();
               $getForm->Show_proD_edit_form($data);
                }
    }
    public function delete_fund(){
        if(!empty($_POST)) {
            $fundID = $_POST['FundID'];
            if($fundID > 0){
                $proD_fund = new SubmitDatas();
                $proD_fund->delete_fund($fundID);
            }
        }
    }
} ?>

