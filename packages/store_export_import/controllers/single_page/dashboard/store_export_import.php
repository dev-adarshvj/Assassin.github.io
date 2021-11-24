<?php  
namespace Concrete\Package\StoreExportImport\Controller\SinglePage\Dashboard;
use Concrete\Package\CommunityStore\Src\CommunityStore\Order\OrderStatus\OrderStatus as StoreOrderStatus;
use Concrete\Package\CommunityStore\Src\CommunityStore\Order\OrderList as StoreOrderList;
use Concrete\Package\CommunityStore\Src\CommunityStore\Order\Order as StoreOrder;
use Concrete\Package\CommunityStore\Src\Attribute\Key\StoreOrderKey as StoreOrderKey;
use \Concrete\Core\Page\Controller\DashboardPageController;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Utilities\Price as Price;

//export
use \Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductList as StoreProductList;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductVariation\ProductVariation as StoreProductVariation;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Product\Product as StoreProduct;
use \Concrete\Package\StoreExportImport\Src\Options;

//import
use Concrete\Core\File\File;
use Concrete\Core\User\User;
//use League\Csv\Reader;

use PageType;
use GroupList;
use Events;

use \Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductFile as StoreProductFile;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductGroup as StoreProductGroup;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductImage as StoreProductImage;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductLocation as StoreProductLocation;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductUserGroup as StoreProductUserGroup;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductRelated as StoreProductRelated;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductOption\ProductOption as StoreProductOption;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Group\Group as StoreGroup;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Group\GroupList as StoreGroupList;
use \Concrete\Package\CommunityStore\Src\Attribute\Key\StoreProductKey;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Tax\TaxClass as StoreTaxClass;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductEvent as StoreProductEvent;



use Loader;
use PageList;
use PageTemplate;
use Page;
use Config;
use Core;

use \Concrete\Core\File\EditResponse as FileEditResponse;
use Concrete\Core\Support\Facade\Application;
use FilePermissions;
use FileImporter;

class StoreExportImport extends DashboardPageController {
	
	public function on_start() {
		
	}
	
	public function view() {
		
		}
	
	
	public function replaceTO($data,$replace,$to){
		
		return preg_replace("'.$replace.'", "$to", $data);	
		
		
	}
	
	public function trimTO($data){
		
		return trim(preg_replace('/\s\s+/', ' ', $data));
		
	}
	
	
	public function export_orders(){
		
		include('include/export/export_order.php');
	
	}
	
	public function export_products(){
		
		
		include('include/export/export_product.php');
		//include('include/export/export_product_format.php');
		
		}
		
	function readCsv($csvFile)
		{
			$row = 1;
			$csvData = array();
			if (($handle = fopen($csvFile, "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					$num = count($data);
				   // echo "<p> $num fields in line $row: <br /></p>\n";
					$row++;
					array_push($csvData, $data);
				  /*  for ($c=0; $c < $num; $c++) {
						echo $data[$c] . "<br />\n";
					}*/
				}
				fclose($handle);
			}	
			
			return $csvData;
		}


	public function imports($type=''){
		
		
			$docRoot = $_SERVER['DOCUMENT_ROOT'];	
			$f = File::getByID($_REQUEST['csv']);
			$csvFilePath = $f->getRelativePath();
			//print_r($f->getRelativePath());
			//die;
			if(is_object($f)){
			$csvData = $this->readCsv($docRoot.$csvFilePath); 
			$fields = $csvData[0];
			unset($csvData[0]);	
				
			/*$resource = $f->getFileResource();
			$reader = Reader::createFromStream($resource->readStream());
			$header = $reader->fetchOne();
			$allData = $reader->fetchAll();
			$reader->setOffset(1);
			$content = $reader->fetchAll();*/
			
			
			//print_r($csvData);
			//die;
			

foreach($csvData as $key =>$data){
				
		$dh = Core::make('helper/date');		
		$db = Loader::db();
		$q = 'SELECT pID FROM CommunityStoreProducts Where pName = "' . $data[1] . '"';
		$prID = $db->GetOne($q);
		
		
			$getDateAdded = $dh->formatCustom('Y-m-d', $data[15]);
			$pDateAdded_h = $dh->formatCustom('h', $data[15]);
			$pDateAdded_m = $dh->formatCustom('i', $data[15]);
			$pDateAdded_a = $dh->formatCustom('a', $data[15]);
		
			
		$pdata = array(
									'pID' => '',
									'pName' => $data[1],
									'pSKU'=> $data[2],
									'pActive' => $data[3],
									'pFeatured' => $data[4],
									'pPrice' => $data[5],
									'pSalePrice' => $data[6],
									'pPriceSuggestions' => '',
									'pPriceMinimum' => $data[7],
									'pPriceMaximum' => $data[8],
									'pTaxable' => $data[9],
									'pTaxClass' => $data[10],
									'pQtyUnlim' => $data[11],
									'pQty' => $data[12],
									'pNoQty' => $data[14],
									'pDateAdded_dt' => $getDateAdded,
									'pDateAdded_h' => $pDateAdded_h,
									'pDateAdded_m' => $pDateAdded_m,
									'pDateAdded_a' => $pDateAdded_a,
									'pDesc' => $data[16],
									'pDetail' => $data[17],
									'noneselection' => '',
									'pShippable' => $data[18],
									'pWeight' => $data[19],
									'pNumberItems' => $data[20],
									'pLength' => $data[21],
									'pWidth' =>$data[22],
									'pHeight' => $data[23],
									'pfID' => '',
									'productImg' => $data[24],
									'multipeImg' => $data[25]
									);
		
		
	if($type!='all'){	
		if($prID > 0)
		
		{
			$pdata['pID']=$prID;
		}			
	}
		//assign multiple image
		/*$multipleImageIds = $this->UploadImageToFilemanager($pdata,'multiple');
		if($multipleImageIds > 0){
			$pdata['pifID']=$multipleImageIds;
		}*/
		
		//assign single image
		$imageIds = $this->UploadImageToFilemanager($pdata,'single');
		if($imageIds > 0){
			$pdata['pfID']=$imageIds['0'];
		}
		//$this->save($pdata);
		$product = StoreProduct::saveProduct($pdata);
		
		
}
$this->redirect('/dashboard/store_export_import', 'imported');			
	}
			
		
		
		
	}

	
	public function import_products(){
		
		
		if($_REQUEST['opt']=='1'){
			
			$this->imports();
			
			}
		
		
		
		if($_REQUEST['opt']=='2'){
		
			
			$db = Loader::db();
			$q = 'DELETE FROM CommunityStoreProducts';
			$db->Execute($q);
			$this->imports();
			
		}
		
		
		if($_REQUEST['opt']=='3'){
			$this->imports('all');
		}
			
}	
	
public function save($data){
		
		
		
        if($data['pID']){
            $this->edit($data['pID']);
        } else{
            $this->add();
        }
		
		
       
            $errors = $this->validate($data);
            $this->error = null; //clear errors
            $this->error = $errors;
            if (!$errors->has()) {

                $originalProduct = false;

                if ($data['pID']) {
                    $product = StoreProduct::getByID($data['pID']);
                    $originalProduct = clone $product;
                    $originalProduct->setID($data['pID']);
                }

                    // if the save sent no options with variation inclusion, uncheck the variations box
                if (isset($data['poIncludeVariations'])) {
                    $allowVariations = false;

                    foreach($data['poIncludeVariations'] as $variationInclude) {
                        if ($variationInclude == 1) {
                            $allowVariations = true;
                        }
                    }

                    if (!$allowVariations) {
                        $data['pVariations'] = 0;
                    }
                }
				
				//save the product
                $product = StoreProduct::saveProduct($data);
                //save product attributes
                $aks = StoreProductKey::getList();
                foreach($aks as $uak) {
                    $uak->saveAttributeForm($product);
                }
              
				 //save images
                StoreProductImage::addImagesForProduct($data,$product);
 				
				//save product groups
                StoreProductGroup::addGroupsForProduct($data,$product);
              
                //save product user groups
                StoreProductUserGroup::addUserGroupsForProduct($data,$product);
                
                //save product options
                StoreProductOption::addProductOptions($data,$product);
                
                //save files
                StoreProductFile::addFilesForProduct($data,$product);
                
                //save category locations
                StoreProductLocation::addLocationsForProduct($data,$product);

                // save variations
                StoreProductVariation::addVariations($data,$product);

                // save related products
                StoreProductRelated::addRelatedProducts($data, $product);

				$product->reindex();

                // create product event and dispatch
                if (!$originalProduct) {
                    $event = new StoreProductEvent($product);
                    Events::dispatch('on_community_store_product_add', $event);
                } else {
                    $event = new StoreProductEvent($originalProduct, $product);
                    Events::dispatch('on_community_store_product_update', $event);
                }

                if($data['pID']){
                    //$this->redirect('/dashboard/store/products/edit/' . $product->getID(), 'updated');
                } else {
                    //$this->redirect('/dashboard/store/products/edit/' . $product->getID(), 'added');
                }
            }//if no errors
        }//if post
    	
	
	
	//$this->redirect('/dashboard/store_export_import', 'imported');
		

	
 public function add()
    {
        
		$this->loadFormAssets();
        $this->set("actionType",t("Add"));
        
        $grouplist = StoreGroupList::getGroupList();
        $this->set("grouplist",$grouplist);
        foreach($grouplist as $productgroup){
            $productgroups[$productgroup->getGroupID()] = $productgroup->getGroupName();
        }
        $this->set("productgroups",$productgroups);

        $gl = new GroupList();
        $gl->setItemsPerPage(1000);
        $gl->filterByAssignable();
        $usergroups = $gl->get();

        $usergrouparray = array();

        foreach($usergroups as $ug) {
            if ( $ug->gName != 'Administrators') {
                $usergrouparray[$ug->gID] = $ug->gName;
            }
        }

        $targetCID = \Config::get('community_store.productPublishTarget');

        $productPublishTarget = false;

        if ($targetCID > 0) {
            $parentPage = \Page::getByID($targetCID);
            $productPublishTarget =  ($parentPage && !$parentPage->isError());
        }

        $this->set('productPublishTarget',$productPublishTarget);
        $this->set('page',false);
        $this->set('pageTitle', t('Add Product'));
        $this->set('usergroups', $usergrouparray);
    }		
	

    public function loadFormAssets()
    {
        $this->requireAsset('core/file-manager');
        $this->requireAsset('core/sitemap');
        $this->requireAsset('css', 'select2');
        $this->requireAsset('javascript', 'select2');

        $this->set('al', Core::make('helper/concrete/asset_library'));

        $this->requireAsset('css', 'communityStoreDashboard');
        $this->requireAsset('javascript', 'communityStoreFunctions');

        $attrList = StoreProductKey::getList();
        $this->set('attribs',$attrList);
        
        $pageType = PageType::getByHandle("store_product");
        $templates = array();

        if ($pageType) {
            $pageTemplates = $pageType->getPageTypePageTemplateObjects();

            foreach ($pageTemplates as $pt) {
                $templates[$pt->getPageTemplateID()] = $pt->getPageTemplateName();
            }
        }
        $this->set('pageTemplates',$templates);
        $taxClasses = array();
        foreach(StoreTaxClass::getTaxClasses() as $taxClass){
            $taxClasses[$taxClass->getID()] = $taxClass->getTaxClassName();
        }
        $this->set('taxClasses',$taxClasses);
    }



public function validate($args)
    {
        $e = Core::make('helper/validation/error');
        
        if($args['pName']==""){
            $e->add(t('Please enter a Product Name'));
        }
        if(strlen($args['pName']) > 255){
            $e->add(t('The Product Name can not be greater than 255 Characters'));
        }
        if(!is_numeric($args['pQty']) && !$args['pQtyUnlim']){
            $e->add(t('The Quantity must be set, and numeric'));
        }
        if(!is_numeric($args['pWidth'])){
            $e->add(t('The Product Width must be a number'));
        }
        if(!is_numeric($args['pHeight'])){
            $e->add(t('The Product Height must be a number'));
        }
        if(!is_numeric($args['pLength'])){
            $e->add(t('The Product Length must be a number'));
        }
        if(!is_numeric($args['pWeight'])){
            $e->add(t('The Product Weight must be a number'));
        }
        
        return $e;
        
    }







public function imported()
    {
        $this->set("success",t("Products Successfully Imported"));
        $this->view();
    }



public function UploadImageToFilemanager($data,$type){
	
	
	$incoming_urls = array();
	
/*	print_r($data);
	die;
	$singleimage = array();
	$multipleimage = array();
	foreach($data as $image){
		$singleimage = 	$image['productImg'];
		$multipleimage[] = 	explode(',',$image['multipeImg']);
		
	}
	*/
	if($type =='single'){
	$imagePath = 	explode(',',$data['productImg']);
	}elseif($type =='multiple'){
	$imagePath = 	explode(',',$data['multipeImg']);
	}
	
	/*print_r($multipleimage);
	die;*/
	
	/*$incoming_urls = array(
	'0' => 'http://rabofla.com/Images/Header4.jpg',
	'1' => 'http://rabofla.com/Images/Corrosion.jpg',
	
	);*/
	
	
	//print_r($incoming_urls);
	$r = new FileEditResponse();
	$app = Application::getFacadeApplication();
	$file = $app->make('helper/file');
	$cf = $app->make('helper/file');
	$fp = FilePermissions::getGlobal();
	$import_responses = array();
    foreach ($imagePath as $this_url) {
        // try to D/L the provided file
		
		//new 5.8 version
			   //$client = $app->make('http/client');
				//$request = $client->getRequest();
			   // $request->setUri($this_url);
			     //$response = $client->send();
		//new 5.8 version
		
		//OLD version 5.8.1
		$request = new \Zend\Http\Request();
        $request->setUri($this_url);
        $client = new \Zend\Http\Client();
        $response = $client->dispatch($request);
        if ($response->isSuccess()) {
            $headers = $response->getHeaders();
            $contentType = $headers->get('ContentType')->getFieldValue();

            $fpath = $file->getTemporaryDirectory();

            // figure out a filename based on filename, mimetype, ???
            if (preg_match('/^.+?[\\/]([-\w%]+\.[-\w%]+)$/', $request->getUri(), $matches)) {
                // got a filename (with extension)... use it
                $fname = $matches[1];
            } elseif ($contentType) {
                // use mimetype from http response
                $fextension = $app->make('helper/mime')->mimeToExtension($contentType);
                if ($fextension === false) {
                    $error->add(t('Unknown mime-type: %s', $contentType));
                } else {
                    // make sure we're coming up with a unique filename
                    do {
                        // make up a filename based on the current date/time, a random int, and the extension from the mime-type
                        $fname = date('Y-m-d_H-i_') . mt_rand(100, 999) . '.' . $fextension;
                    } while (file_exists($fpath.'/'.$fname));
                }
            } //else {
                // if we can't get the filename from the file itself OR from the mime-type I'm not sure there's much else we can do
            //}

            if (strlen($fname)) {
                // write the downloaded file to a temporary location on disk
                $handle = fopen($fpath.'/'.$fname, "w");
                fwrite($handle, $response->getBody());
                fclose($handle);

                // import the file into concrete
                if ($fp->canAddFileType($cf->getExtension($fname))) {

                    $folder = null;
                    if (isset($_POST['currentFolder'])) {
                        $node = \Concrete\Core\Tree\Node\Node::getByID($_POST['currentFolder']);
                        if ($node instanceof \Concrete\Core\Tree\Node\Type\FileFolder) {
                            $folder = $node;
                        }
                    }

                    if (!$fr && $folder) {
                        $fr = $folder;
                    }


                    $fi = new FileImporter();
                    $resp = $fi->import($fpath.'/'.$fname, $fname, $fr);
                    $r->setMessage(t('File uploaded successfully.'));
                    if (is_object($fr)) {
                        $r->setMessage(t('File replaced successfully.'));
                    }
                } else {
                    $resp = FileImporter::E_FILE_INVALID_EXTENSION;
                }
                if (!($resp instanceof \Concrete\Core\Entity\File\Version)) {
                    $error->add($fname . ': ' . FileImporter::getErrorMessage($resp));
                } else {
					
					//return  $import_responses[] = $resp;
                   $import_responses[] = $resp->getFileID();
				  

                    if (!($fr instanceof \Concrete\Core\Entity\File\Version)) {
                        // we check $fr because we don't want to set it if we are replacing an existing file
                        $respf = $resp->getFile();
                        $respf->setOriginalPage($_POST['ocID']);
                        $files[] = $respf;
                    } else {
                        $respf = $fr;
                    }
                }

                // clean up the file
                unlink($fpath.'/'.$fname);
            } else {
                // could not figure out a file name
                $error->add(t(/*i18n: %s is an URL*/'Could not determine the name of the file at %s', h($this_url)));
            }
        } else {
            // warn that we couldn't download the file
            $error->add(t(/*i18n: %s is an URL*/'There was an error downloading %s', h($this_url)));
        }
    }
		
 return $import_responses;
	
}



}