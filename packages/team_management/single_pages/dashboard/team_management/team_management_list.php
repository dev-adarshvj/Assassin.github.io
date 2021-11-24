<!--List single page view-->
<?php
		if($remove_name){
      echo die('gfygfughug');
		?>

<div class="alert-message block-message error" style="  background-color: #4674a1;padding: 20px; margin-bottom: 20px; color:#FFF;"> <a class="close" href="<?php  echo $this->action('clear_warning');?>">×</a>
  <p><strong style="font-size:20px;">
    <?php  echo t('This is a warning!');?>
    </strong></p>
  <p>
    <?php  echo t('Are you sure you want to delete ').t($remove_name).'?';?>
  </p>
  <p>
    <?php  echo t('This action may not be undone!');?>
  </p>
  <div class="alert-actions" > <a class="btn btn-danger" href="<?php  echo BASE_URL;?>/index.php/dashboard/team_management/team_management_list/delete/<?php  echo $remove_cid;?>/<?php  echo $remove_name;?>/">
    <?php  echo t('Yes Remove This');?>
    </a> <a class="btn btn-primary" href="<?php  echo $this->action('clear_warning');?>">
    <?php  echo t('Cancel');?>
    </a> </div>
</div>
<?php
		}
		?>
<form method="get" action="<?php  echo $this->action('view')?>"  style="float:left; margin-bottom:20px; width:100%;">
  <?php

	 $form = Loader::helper('form');
		$sections[0] = '** All';
		asort($sections);
		?>
  <table class="ccm-results-list" style="float:left;">
    <tr>
      <th><strong><?php echo $form->label('cParentID', t('Category')) ;?></strong> </th>
      <th><strong>
        <?php  echo t('By Name')?>
        </strong></th>
      <th></th>
    </tr>
    <tr>
      <td><?php
						//rsort($sections);
						 echo $form->select('cParentID', $sections, $cParentID)?></td>
      <td><?php  echo $form->text('like', $like)?></td>
      <td><?php echo $form->select('numResults', array(
				'10' => '10',
				'20' => '20',
				'50' => '50',
				'100' => '100',
				'250' => '250',
				'500' => '500',
				'1000' => '1000',
			), $_REQUEST['numResults'])?></td>
        </td>
      <td><?php echo $form->submit('submit', t('Search'))?></td>
    </tr>
  </table>
  <div class="right" style="float:right; margin-top:40px;"><a class="btn btn-success" href="<?php  echo BASE_URL;?>/index.php/dashboard/team_management/add_edit">Add Team</a></div>
</form>
<br/>
<?php $nh = Loader::helper('navigation');
		$fm = Loader::helper('form');
		$dh = Core::make('helper/date');
		/*$ccm_order_dir='desc';*/
		if ( count($pageList) > 0) { ?>
<table border="0" class="ccm-results-list table table-striped blog_list_icon" cellspacing="0" cellpadding="0">
  <tr>
    <th width="25%" class="blog_list_th_class_bg"> <a href="<?php  echo BASE_URL;?>/index.php/dashboard/team_management/team_management_list?ccm_order_by_name=cvName&ccm_order_dir_name=<?php if($_GET['ccm_order_by_name']=='cvName'){ echo $ccm_order_dir_name;}else {echo 'asc';} ?>">
      <?php  echo t('Name')?>
      </a>
      <?php if($_GET['ccm_order_dir_name']=='asc'){ echo '<i class="fa fa-sort-asc"></i>';}else {echo '<i class="fa fa-sort-desc"></i>';} ?>
    </th>
    <th width="20%" class="blog_list_th_class_bg"> <a href="<?php  echo BASE_URL;?>/index.php/dashboard/team_management/team_management_list?ccm_order_by_date=cvDatePublic&ccm_order_dir_date=<?php if($_GET['ccm_order_by_date']=='cvDatePublic'){ echo $ccm_order_dir_date;}else {echo 'asc';} ?>">
      <?php  echo t('Date')?>
      <?php if($_GET['ccm_order_dir_date']=='asc'){ echo '<i class="fa fa-sort-asc"></i>';}else {echo '<i class="fa fa-sort-desc"></i>';} ?>
      </a> </th>
    <th width="15%" class="blog_list_th_class_bg"> <a href="<?php  echo BASE_URL;?>/index.php/dashboard/team_management/team_management_list?ccm_order_by_cat=cvName&ccm_order_dir_cat=<?php if($_GET['ccm_order_by_cat']=='cvName'){ echo $ccm_order_dir_cat;}else {echo 'asc';} ?>">
      <?php  echo t('Category')?>
      </a>
      <?php if($_GET['ccm_order_dir_cat']=='asc'){ echo '<i class="fa fa-sort-asc"></i>';}else {echo '<i class="fa fa-sort-desc"></i>';} ?>
    </th>
    <th width="30%" class="blog_list_th_class_bg" > <?php  echo t('Actions')?></th>
  </tr>
  <?php
			//display page content
			foreach($pageList as $cobj) {
				$i++;
				$t++;
				if(is_object($cobj)){

					$section_id = $cobj->getCollectionParentID();
					$sec_page= Page::getByID($section_id);
					if($sec_page->cParentID!=1){
					$prefix=Page::getByID($sec_page->cParentID)->getCollectionName().'-';
					}else{
						$prefix='';
					}
					$page_section = $prefix.$sec_page->getCollectionName();
					$pkt = Loader::helper('concrete/urls');

				}
			?>
  <tr>
    <td style="padding:30px 30px 30px 5px !important;" class="align_top"><a href="<?php   echo $nh->getLinkToCollection($cobj)?>">
      <?php   echo $cobj->getCollectionName()?>
      </a></td>
   
      
       <td class="align_top"  style="padding:30px 0px 30px 0px !important;"><?php
                   /* if ($cobj->getCollectionDatePublic() > date(DATE_APP_GENERIC_MDYT_FULL)) {
                        echo '<font style="color:green;">';
                        echo date('M d, Y', strtotime($cobj->getCollectionDatePublic()));
                        echo '</font>';
                    } else {*/
                        echo date('M d, Y', strtotime($cobj->getCollectionDatePublic()));
                 //   }
                    ?></td>
      
      
    <td class="align_top"  style="padding:30px 30px 30px 5px !important;"><?php echo Page::getByID($cobj->cParentID)->getCollectionName(); ?></td>
    <td class="align_top"  style="padding:30px 0px 30px 0px !important;"><a href="<?php   echo $this->url('/dashboard/team_management/add_edit', 'edit', $cobj->getCollectionID())?>" class="pagetooltip btn btn-primary">Edit</a> <a href="<?php  echo $this->url('/dashboard/team_management/team_management_list', 'delete_check', $cobj->getCollectionID(),$cobj->getCollectionName())?>" class="pagetooltip btn btn-danger">Delete</a></td>
  </tr>
  <?php    } ?>
</table>
<br/>
<?php  echo $pagination;?>
<?php } else {
			print t('No page entries found.');
		}
		?>
<div class="ccm-search-results-pagination"></div>
<style type="text/css">


.ccm-pagination-wrapper{text-align:center;}

.blog_list_icon .fa{
	top: -2px;
  left: 5px;
  position: relative;
  font-size:11px;
  color: #eaeaea;
}
.blog_list_th_class_bg{background-color: #006699 !important;
  color: #eaeaea !important;
  font-weight: 300; padding: 10px !important;}

.blog_list_th_class_bg a{ color: #eaeaea !important;}
th.blog_list_th_class_bg:hover{  background: #00496e !important;
 }

.blog_list_icon i.fa.fa-sort-asc{top:2px;color: #eaeaea;}

.status.not-publish{
color:#d43f3a;
		}

		.status.not-publish:hover{text-decoration:underline; color:#d43f3a !important;}

.status.publish{color:#4cae4c;}
.btn-yellow{background-color:#eae664;
  border-color: #f3ed08; color:#fff !important;}


.ccm-ui td, .ccm-ui th {
  padding: 5px;
}

		.align_top .icon {
  display: block;
  float: left;
  height: 20px;
  width: 20px;
  background-image: url('/concrete/images/icons_sprite.png');
}
.edit {
  background-position: -22px -2225px;
  margin-right: 6px!important;
}
.copy {
  background-position: -22px -439px;
  margin-right: 6px!important;
}
.delete {
  background-position: -22px -635px;

}
td.align_top{
	  padding: 2px !important;
  line-height: 2.428571 !important;
		}
		</style>
