<!--Add/Edit single page view-->
<?php
	$df = Loader::helper('form/date_time');
	$form = Loader::helper('form');
	$dh = Core::make('helper/date');
	Loader::model("attribute/categories/collection");
	global $u;
	$u=new User();
	$uID=$u->uID;
	//get page name/desc
	if (is_object($page)) {
		//2020-07-19 08:56:00
		//$date = $dh->formatCustom('d/m/y',$page->getCollectionDatePublic());
		$date = $page->getCollectionDatePublic('Y-m-d m:i:s');
		$page_title = $page->getCollectionName();
		$pageDescription = $page->getCollectionDescription();
		$pageDate = $date;
		$cParentID = $page->getCollectionParentID();
		$ctID = $page->getCollectionTypeID();

		$uID=$page->getCollectionUserID();
		$task = 'update';
		$buttonText = t('Update Team');
		$title = 'Update';
	} else {
		$task = 'add';
		$buttonText = t('Add Team Entry');
		$title = 'Add';
		$pageDate = date('Y-m-d m:i:s');
	}



	$fullwidth='style="width:100%;"'?>
<?php   if ($this->controller->getTask() == 'edit') { ?>

<form method="post" class="form-horizontal" action="<?php  echo $this->action($task)?>" id="page-form">
<?php  echo $form->hidden('pageID', $page->getCollectionID())?>
<?php   }else{ ?>
<form method="post" class="form-horizontal" action="<?php  echo $this->action($task)?>" id="page-form">
  <?php  } ?>

  <fieldset>
    <div class="row">
      <div class="form-group">
        <?php  echo $form->label('cParentID', t('Section'),array('class'=>'col-sm-3'))?>
        <div class="col-sm-7">
          <div class="input-group">
            <?php   if (count($sections) == 0) { ?>
            <?php  echo t('No sections defined. Please create a page with the attribute "team_management_section" set to true.')?>
            <?php   } else { ?>
            <?php  echo $form->select('cParentID', $sections, $cParentID)?>
            <?php   } ?>
            <span class="input-group-addon"><i class="fa fa-asterisk"></i></span> </div>
        </div>
      </div>
    </div>
    <?php
   Loader::model('config');
 echo  $form->hidden('ctID',Config::get('concrete.team_management_page_type_id'));
 echo  $form->hidden('ptID',Config::get('concrete.team_management_page_template_id')) ;
  ?>
    <div class="row date_section">
      <div class="form-group">
        <?php  echo $form->label('page_date_time', t('Date/Time'),array('class'=>'col-sm-3'))?>
        <div class="col-sm-7">
          <div class="input-group">
            <?php
			echo $df->datetime('page_date_time', $pageDate)?>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="form-group">
        <?php  echo $form->label('page_title', t('Name'),array('class'=>'col-sm-3'))?>
        <div class="col-sm-7">
          <div class="input-group">
            <?php  echo $form->text('page_title', $page_title)?>
            <span class="input-group-addon"><i class="fa fa-asterisk"></i></span> </div>
        </div>
      </div>
    </div>
   <!-- <div class="row">
      <div class="form-group">
        <?php  echo $form->label('pageDescription', t('Short Description'),array('class'=>'col-sm-3'))?>
        <div class="col-sm-7">
          <div class="input-group" <?php echo $fullwidth;?>>
            <?php   echo $form->textarea('pageDescription', $pageDescription)?>
          </div>
        </div>
      </div>
    </div>-->
  </fieldset>
  <fieldset>
<!--  display team page attributes-->
    <?php Loader::model('config');
	$attributeset_id=Config::get('concrete.team_management_attribute_set_id');
     $set = AttributeSet::getByID($attributeset_id);
	if(is_object($set)){
		$setAttribs = $set->getAttributeKeys();
		foreach ($setAttribs as $ak) {
					if(is_object($page)) {
						$aValue = $page->getAttributeValueObject($ak);
					}
					echo '<div class="row">
      <div class="form-group">
		';
		 echo $form->label($ak->getAttributeKeyHandle(), t($ak->getAttributeKeyName()),array('class'=>'col-sm-3'));
					echo '  <div class="col-sm-7">
          <div class="input-group" '.$fullwidth.'>';

					echo $ak->render('form', $aValue);
					echo '</div>
        </div>
      </div>
    </div>
     ';
	}} ?>


</fieldset>


  <div class="ccm-dashboard-form-actions-wrapper">
    <div class="ccm-dashboard-form-actions"> <a href="<?php echo View::url('/dashboard/team_management/team_management_list')?>" class="btn btn-default pull-left"><?php echo t('Cancel')?></a> <?php echo Loader::helper("form")->submit('add', t($title.' Team'), array('class' => 'btn btn-primary pull-right'))?> </div>
  </div>
  </div>
</form>
<style type="text/css">
.redactor-box textarea{min-height:300px;}
div#defaultContent {
  height: 400px;
}
body {
	overflow: auto !important;
}
.ccm-ui .date_section .input-group input {
    width: 30% ;
}
.ccm-ui .date_section .input-group select {
    width: 20%;
}
.fancytree-plain > li > .fancytree-node .fancytree-checkbox
{
  display:none;
}

</style>
