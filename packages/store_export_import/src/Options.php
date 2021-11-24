<?php
namespace Concrete\Package\StoreExportImport\Src;
use \Concrete\Core\Legacy\Model;
use Loader;
use \Concrete\Core\Legacy\DatabaseItemList;
class Options extends model {
	

	public function getOptionData($ID){
			if($ID){
				
				return Loader::db()->getAll('SELECT * from communitystoreproductvariations where pID='.$ID);
				
			}
		
	}
	public function getOptionLabel($ID){
			if($ID){
				
				return Loader::db()->getOne('SELECT poiName from CommunityStoreProductOptionItems where poiID='.$ID);
				//return Loader::db()->getOne('SELECT poiName from communitystoreproductoptionitems where poiID='.$ID);
				
				
				
			}
		
	}
	

	public function innerJoin(){
	
			return Loader::db()->getArray('select pvPrice, pvSalePrice, poiName from communitystoreproductvariations cv left join communitystoreproductoptionitems co on cv.pID = co.poID');
		
		
	}




}