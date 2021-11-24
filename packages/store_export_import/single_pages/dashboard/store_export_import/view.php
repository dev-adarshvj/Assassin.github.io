<?php
defined('C5_EXECUTE') or die("Access Denied.");
$dh = Core::make('helper/date');
use \Concrete\Package\CommunityStore\Src\CommunityStore\Utilities\Price as Price;
use \Concrete\Package\CommunityStore\Src\Attribute\Key\StoreOrderKey as StoreOrderKey;
?>




<div id="ccm-dashboard-content-inner export-import">
  <div class="row border export-order-section">
    <div class="col-xs-12 col-sm-12">
      <div class="row content-margin">
        <div class="col-xs-6 col-sm-6 text-content">
          <h5><i class='fa fa-download'></i>&nbsp; &nbsp;<?php echo t('Export Orders'); ?></h5>
        </div>
        <div class="col-xs-6 col-sm-6 link-text"> <a href="<?php echo $this->action('export_orders')?>" class="pull-right btn btn-success"><?php echo t('Export CSV')?></a> </div>
      </div>
    </div>
  </div>
  
  
  
  <div class="row border export-product-section">
    <div class="col-xs-12 col-sm-12">
      <div class="row content-margin">
        <div class="col-xs-6 col-sm-6 text-content">
          <h5><i class='fa fa-download'></i>&nbsp; &nbsp;<?php echo t('Export Products'); ?></h5>
        </div>
        <div class="col-xs-6 col-sm-6 link-text"> <a href="<?php echo $this->action('export_products')?>" class="pull-right btn btn-success"><?php echo t('Export CSV')?></a> </div>
      </div>
    </div>
  </div>
  
  <form action="<?php echo $this->action('import_products')?>" id="importProduct" >
  <div class="row border import-section">
    <div class="col-xs-12 col-sm-12">
      <div class="row content-margin">
        <div class="col-xs-12 col-sm-3 text-content">
          <h5><i class='fa fa-upload'></i>&nbsp; &nbsp;<?php echo t('Import Products'); ?></h5>
        </div>
        <div class="col-xs-12 col-sm-5 options"> 
        
            <input type="radio" name="opt" value="1" checked> Overwrite existing matching products<br>
            <input type="radio" name="opt" value="2"> Delete all existing products first<br>
            <input type="radio" name="opt" value="3"> Upload in addition to already existing products /skip product with same SKU 
        
       	
        </div>
        <div class="col-xs-12 col-sm-3 fileselector" style="margin-bottom:20px;"> 
        
		<?php 
		        $html = Core::make('helper/concrete/file_manager');
                echo $html->file('csv', 'csv', t('Choose CSV'));

		?>
        
        </div>
        
        <div class="col-xs-12 col-sm-1 link-text"> 
		<a href="javascript:void(0);" onclick="$('#importProduct').submit();" class="pull-right btn btn-success"><?php echo t('Import')?></a> </div>
      </div>
    </div>
  </div>
  </form>
  
  
</div>
<style type="text/css">
.border {
	color: #333333;
	background-color: #f5f5f5;
	border: 2px solid #dddddd;
	padding: 30px;
	margin-bottom:50px;
	}


</style>
