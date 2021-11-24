<?php  defined('C5_EXECUTE') or die(_("Access Denied."));
$uh = Loader::helper('concrete/urls');
$df = Loader::helper('form/date_time');
$form = Loader::helper('form');
$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication(); ?>
<div class="manage_teacher_wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="javascript:void(0)" onclick="$('ul.nav li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide(); $('div.pro_d_funds').show();" data-toggle="tab">
                            <?php echo t('Manage Pro-D Funds'); ?>
                        </a>
                    </li>
                    <li class="detailTitle">
                        <a href="javascript:void(0)" onclick="$('ul.nav li').removeClass('active'); $(this).parent().addClass('active'); $('.pane').hide(); $('div.manage_teacher').show();" data-toggle="tab">
                            <?php   echo t('Manage Teachers') ?>
                        </a>
                    </li>
                    <li class="detailTitle">
                        <a href="<?php   echo $this->url('/dashboard/professional_management/manage_teachers', 'export_fund')?>"><?php echo t('Export Pro-D Data') ?></a>
                    </li>
                    <li class="detailTitle">
                        <a href="<?php echo $this->url('/dashboard/professional_management/manage_teachers', 'export_teacher')?>" ><?php echo t('Export Teachers') ?></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="pane pro_d_funds" style="display: block;">
            <div class="row">
                <h2><?php echo t('Teacher Pro-D Summary'); ?></h2>
                <div class="manage_fund_outer">
                    <div class="col-12 col-md-3">
                        <h4>Employee #: <span class="employ-id"></span></h4>
                        <form action="">
                            <div class="input-group">
                                <select name="eid" id="select_employee" class="ccm-input-select form-control">
                                    <?php if(is_array($teachers)){
                                        foreach ($teachers as $key => $teacher) {
                                            $teacher_reg_no = $teacher->getUserName();
                                            $teacher_id = $teacher->getUserID();
                                            $teacher_name = $teacher->getAttributeValue('teacher_name');?>
                    <option data-teacher-id="<?= $teacher_id;?>" value="<?= $teacher_reg_no; ?>" <?= $key==0?'selected="selected"':'';?> ><?php echo $teacher_name; ?></option>
                                        <?php } }?>
                                </select>
                            </div>
                          <div class="ccm-dashboard-form-actions"><input  type="submit" class="btn btn-success update-employ"  value="Update"/></div>
                        </form>
                        <div class="min-t f-right fund_story"></div>
                    </div>
                    <div class="col-12 col-md-3"><div class="fund_used"></div></div>
                    <div class="col-12 col-md-3"><div class="fund_left"></div></div>
                    <div class="col-12 col-md-3"><div class="fund_remedy"></div></div>
                </div>
            </div>
            <div class="fund_msg_box" style="display: none;"></div>
            <div class="row"><div class="col-12"><div class="fund_expense"></div></div>
                <div style="display: none" class="proDfund-form"></div>
                <div style="display: none" class="proDfund-edit-form"></div>
</div>
        </div>
        <div class="pane manage_teacher" style="display: none;">
            <div class="row">
                <div class="col-12">
                    <h1><?php   echo t('Manage Teachers'); ?></h1>
                        <div class="ccm-dashboard-form-actions">
<button class="btn btn-success dashboard_btn pull-left add-teacher-btn" data-dialog-title="Add Teacher" data-dialog-width="650" data-dialog-height="500" data-dialog="add-teacher">+ Add Teacher</button>
                            <a class="btn btn-success dashboard_btn pull-right" onclick="confirmAllEmail(0,0,'all_mail');">Email All</a>
                        </div>
                    <div style="display: none" class="teacher-popup">
                        <div data-dialog-wrapper="add-teacher" class="ccm-ui ui-dialog-content ui-widget-content">
                            <div id="ccm-block-fields">
                                <form method="post" action="<?php echo t('/dashboard/professional_management/manage_teachers/submit'); ?>">
                                    <?= $form->getAutocompletionDisabler() ?>
                                    <fieldset>
                                        <legend><?php echo t('Basic Details'); ?></legend>
                                        <div class="form-group">
                                            <label for="uName" class="control-label"><?php echo t('Username'); ?></label>
                                            <div class="input-group">
                                                <?php echo $form->text('uName', array('autofocus' => 'autofocus', 'autocomplete' => 'off')); ?>
                                                <span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="uPassword" class="control-label"><?php echo t('Password'); ?></label>
                                            <div class="input-group">
                                                <?php echo $form->password('uPassword', array('autocomplete' => 'off')); ?>
                                                <span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="uEmail" class="control-label"><?php echo t('Email Address'); ?></label>
                                            <div class="input-group">
                                                <?php echo $form->email('uEmail'); ?>
                                                <span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
                                            </div>
                                        </div>
                                        <?php if (count($locales)) {  ?>
                                            <div class="form-group">
                                                <label for="uEmail" class="control-label"><?php echo t('Language'); ?></label>
                                                <div>
                                                    <?php echo $form->select('uDefaultLanguage', $locales, Localization::activeLocale()); ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </fieldset>
                                    <?php if (count($attribs) > 0) { ?>
                                        <fieldset>
                                            <legend><?php echo t('Registration Data'); ?></legend>
                                            <?php foreach ($attribs as $ak) {
                                                if (in_array($ak->getAttributeKeyID(), $assignment->getAttributesAllowedArray())) { ?>
                                                    <div class="form-group">
                                                        <label class="control-label"><?php echo $ak->getAttributeKeyDisplayName(); ?></label>
                                                        <div>
                                                            <?php $ak->render(new \Concrete\Core\Attribute\Context\DashboardFormContext(), null, false); ?>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>
                                        </fieldset>
                                    <?php } ?>
                                    <fieldset>
                                        <legend><?php echo t('Groups'); ?></legend>
                                        <div class="form-group">
                                            <label class="control-label"><?php echo t('Place this user into groups'); ?></label>
                                            <?php foreach ($gArray as $g) {
                                                $gp = new Permissions($g);
                                                if ($gp->canAssignGroup()) { ?>
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" name="gID[]" value="<?php echo $g->getGroupID(); ?>"
                                <?php if (isset($_POST['gID']) && is_array($_POST['gID']) && in_array($g->getGroupID(), $_POST['gID'])) { ?> checked <?php } ?>>
                                                            <?php echo $g->getGroupDisplayName(); ?>
                                                        </label>
                                                    </div>
                                                <?php }
                                            } ?>
                                        </div>
                                    </fieldset>
                                    <?php echo $token->output('submit');?>
                                    <div class="user_submit_action">
                                        <fieldset>
                                            <div class="ccm-dashboard-form-actions-wrapper">
                                                <div class="ccm-dashboard-form-actions">
                     <a href="<?php echo View::url('/dashboard/professional_management/manage_teachers'); ?>" class="btn btn-default pull-left"><?php echo t('Cancel'); ?></a>
                                                    <?php echo Loader::helper("form")->submit('add', t('Add'), array('class' => 'btn btn-primary pull-right')); ?>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<div class="row"><div class="col-12"><div class="msg_box" style="display: none;"><div class="alert alert-warning alert-dismissible mail-msg"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>The Mail is sending please wait!!</strong></div></div></div></div>
            <div class="row">
                <div class="col-12">
                    <table width="100%" class="proprty_tb" cellspacing="25">
                        <tbody class="teacher_data"><tr>
                            <th width="100"><label for="section" class="control-label">ID</label> </th>
                            <th width="360"><label for="keyword" class="control-label">Name</label></th>
                            <th width="300"><label for="keyword" class="control-label">Email</label></th>
                            <th width="50"><label for="keyword" class="control-label">Edit</label></th>
                            <th width="100"><label for="keyword" class="control-label">Summary</label></th>
                            <th width="50"><label for="keyword" class="control-label">Delete</label></th>
                            <th width="50"><label for="keyword" class="control-label">Email</label></th>
                        </tr>
                        <?php if(is_array($teachers)){
                            foreach ($teachers as $teacher) {
                                $teacher_reg_no = $teacher->getUserName();
                                $teacher_id = $teacher->getUserID();
                                $teacher_email = $teacher->getUserEmail();
                                $teacher_name = $teacher->getAttributeValue('teacher_name');
                                $fte_2018 = $teacher->getAttributeValue('fte_2018');
                                $fte_2019 = $teacher->getAttributeValue('fte_2019');
                                $fte_2020 = $teacher->getAttributeValue('fte_2020');
                                $ttoc_used_2018 = $teacher->getAttributeValue('ttoc_used_2018');
                                $ttoc_used_2019 = $teacher->getAttributeValue('ttoc_used_2019');
                                $ttoc_used_2020 = $teacher->getAttributeValue('ttoc_used_2020');
                                $remedy = $teacher->getAttributeValue('remedy');
                                $phone_number = $teacher->getAttributeValue('phone_number');
                                $notes = $teacher->getAttributeValue('notes');
                                $user_id = $teacher->getUserID(); ?>
                                <tr>
                                    <td><?php echo $teacher_reg_no; ?></td>
                                    <td><?php echo $teacher_name; ?></td>
                                    <td><?php echo $teacher_email; ?></td>
<td><a class="btn btn-primary edit_teacher" onclick="addRecord(<?php echo $teacher_id; ?>);"><?php echo t('Edit');?></a></td>
<td><a class="btn btn-warning teacher_summary" onclick="viewSummary(<?php echo $teacher_reg_no; ?>);"><?php echo t('Summary');?></a></td>
                                    <td><a data-teacher-id="<?php echo $teacher_id; ?>" class="btn btn-danger delete_teacher"><?php echo t('Delete');?></a></td>
      <td class="teacher_all_mail"><a class="btn btn-info teacher_mail" onclick="confirmEmail(<?php echo $teacher_id; ?>,<?php echo $teacher_reg_no; ?>,'single_mail');"><?php echo t('Email');?></a></td>
                                </tr>
                            <?php } }  ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
  .teacher_outer{
      opacity: 0.1;
      background: rgba(240,240,240,0.95);
      backdrop-filter: blur(4px);
  }
  .mail-msg {
      animation: blinker 1s linear infinite;
      margin-top: 10px;
  }
  @keyframes blinker {
      50% {
          opacity: 0;
      }
  }
  table.proprty_tb { margin-top: 10px; }
  table.proprty_tb tbody tr th { padding: 10px; }
  table.proprty_tb tbody tr td { padding: 5px; }
  table#expense-table tbody tr th {
      padding-left: 4px;
      padding-right: 2px;
  }
  table#expense-table tbody tr td { padding: 5px; }
</style>

<!-- Manage Teacher -->
<script>
    function confirmAllEmail(teacher_id,teacher_reg_no,status) {
     var confirm_msg = 'Are you sure you would like to email the report to all teachers?';
        $('td.teacher_all_mail a').addClass('no_cofirm');
        if(confirm(confirm_msg)) {
            $(".msg_box").show();
            $('td.teacher_all_mail a').each(function (index, value) {
                $(this).trigger("click");
            });
        }
        $('td.teacher_all_mail a').removeClass('no_cofirm');
    }

    function confirmEmail(teacher_id,teacher_reg_no,status) {
        var confirm_msg = 'Are you sure you would like to email the report?';
if($(".no_cofirm").length){
    mail_sent(teacher_id,teacher_reg_no,status,100);
}else{
    if(confirm(confirm_msg)) {
        mail_sent(teacher_id,teacher_reg_no,status,5000);
    } else { return false; }
}
    }
    function mail_sent(teacher_id,teacher_reg_no,status,time){
        $.ajax({
            url: '/send_mail',
            type: 'POST',
            data: jQuery.param({
                teacher_id: teacher_id,
                teacher_reg_no:teacher_reg_no,
                status:status
            }),
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            success: function (response) {
                var jsonData = $.parseJSON(response);
                var result = jsonData;
                $('.msg_box').html(result);
                $(".msg_box").show().delay(time).fadeOut();
            },
            error: function () { alert("error"); }
        });
    }
    $(".delete_teacher").on("click", function () {
         var teacherId = $(this).data("teacher-id");
         var DeleteUrl = '<?= URL::to('/index.php/dashboard/professional_management/manage_teachers/delete_teacher/');?>';
        if(confirm('Are you sure you want to delete this record?')) {
            $.ajax({
                url: DeleteUrl,
                type: 'POST',
                data: jQuery.param({id: teacherId}),
                contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                success: function (response) {
                    get_all_data();
                },
                error: function () { alert("error"); }
            });
        } else { return false; }
     });
        function addRecord(teacher_id) {
            console.log(teacher_id);
            $.fn.dialog.open({
                width: 1000,
                height: 500,
                modal: false,
                href: '/dashboard/users/search/view/'+teacher_id,
                title: 'Edit Teacher Record'
            });
        }
    function get_all_data(){
        $.ajax({
            url: '<?= URL::to('/dashboard/professional_management/manage_teachers/view_all_data');?>',
            type: 'POST',
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            success: function (response) {
                response = JSON.parse(response);
                var result = response.res_html;
                $('tbody.teacher_data').html(result);
            },
            error: function () {
                alert("error");
            }
        });
    }
    $( ".edit_teacher" ).click(function() {
        $('.manage_teacher_wrapper').addClass('teacher_outer');
        setTimeout(function() {
            $('header.ccm-dashboard-page-header').css('display','none');
            $('html.ccm-panel-right div#ccm-dashboard-content').css('margin-right','0px');
            $( ".user_submit_action div" ).removeClass( "ccm-dashboard-form-actions-wrapper" );
            $('.ccm-ui button:last-child').css('display','none');
            $('body').addClass('normal_way');
            $('.user_form_outer form').attr('action','/dashboard/professional_management/manage_teachers/submit');
        }, 4000);
    });

        $(window).click(function(e) {
            if($('body.normal_way').css('overflow')=="visible") {
                $('html.ccm-panel-right div#ccm-dashboard-content').css('margin-right','320px');
                $("header.ccm-dashboard-page-header").css('display', 'block');
                $('.manage_teacher_wrapper').removeClass('teacher_outer');
                $('body').removeClass('normal_way');
                $('.ccm-ui button:last-child').css('display','block');
            }
        });

<!-- Manage Pro-D Funds -->
   function confirmDeleteRecord(FundID){
    if(FundID>0){
        if(confirm('Are you sure you want to delete this record?')) {
            $.ajax({
                url: '<?= URL::to('/delete_fund');?>',
                type: 'POST',
                data: jQuery.param({FundID: FundID}),
                contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                success: function (response) {
                   if(response){
                       response = JSON.parse(response);
                       $('.fund_msg_box').html(response);
                       $(".fund_msg_box").show().delay(5000).fadeOut();
                       $( ".update-employ").trigger('click');
                   }
                },
                error: function () { alert("error"); }
            });
        } else { return false; }
    }
    }
     function proD_fund(employ_id,id){
        if(employ_id>0){
            var status = "add";
            $.ajax({
                url: '/employ_details',
                type: 'POST',
                data: {employ_id : employ_id,id:id,status:status},
                contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                success: function (response) {
                    response = JSON.parse(response);
                    $('.fund_story').html(response.fund_story);
                    $('.fund_used').html(response.fund_used);
                    $('.fund_left').html(response.fund_left);
                    $('.fund_remedy').html(response.fund_remedy);
                    $('.fund_expense').html(response.fund_expenses);
                    $('.proDfund-form').html(response.proDform);
                },
                error: function () {
                    alert("error");
                }
            });
        }
    }
  function viewSummary(teacher_id){
 $("#select_employee option").each(function(i){
if($(this).val() == teacher_id ){
$("#select_employee option").removeAttr("selected");
$(this).attr("selected","selected");
$( ".update-employ").trigger('click');
$('ul.nav.nav-tabs li:first-child a').trigger('click');
}
    });
}

    $(document).ready(function () {
        var employ_id = $('#select_employee').find(":selected").attr('value');
        $('span.employ-id').html(employ_id);
        var id = $('#select_employee').find(":selected").data('teacher-id');
        proD_fund(employ_id,id);
    });
    $( ".update-employ" ).click(function(e) {
        e.preventDefault();
            var employ_id = $('#select_employee').find(":selected").attr('value');
            $('span.employ-id').html(employ_id);
            var id = $('#select_employee').find(":selected").data('teacher-id');
            proD_fund(employ_id,id);
    });
   function editproDform(id,employ_name,status){
   $('.manage_teacher_wrapper').addClass('teacher_outer');
   var status = status.trim();
         $.ajax({
             url: "/edit_prodForm",
             type: 'POST',
             data: { proDfundID: id, employ_name: employ_name,status:status },
             success: function (response) {
                 response = JSON.parse(response);
                 $('.proDfund-edit-form').html(response);
             $.fn.dialog.open({
                     width: 500,
                     height: 700,
                     modal: false,
                     element:'.proDfund-edit-form',
                     title: 'Edit Pro-D Fund'
                 });
                 $('body').addClass('normal_way');
           }
       });
    }
   function addproDfund(employ_id,id) {
       $('.manage_teacher_wrapper').addClass('teacher_outer');
        $.fn.dialog.open({
            width: 500,
            height: 700,
            modal: false,
            element:'.proDfund-form',
            title: 'Add Pro-D Fund'
        });
       $('body').addClass('normal_way');
    }
   </script>
