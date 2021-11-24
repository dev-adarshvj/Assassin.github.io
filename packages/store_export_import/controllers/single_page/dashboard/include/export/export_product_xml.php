<?php  
defined('C5_EXECUTE') or die("Access Denied.");
use \Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductList as StoreProductList;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductVariation\ProductVariation as StoreProductVariation;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Product\Product as StoreProduct;
use \Concrete\Package\StoreExportImport\Src\Options;
use \Concrete\Package\CommunityStore\Src\Attribute\Key\StoreProductKey;

		
		$dh = Core::make('helper/date');
        $products = new StoreProductList();
        $products->setItemsPerPage(1000);
        $products->setGroupID($gID);
        $products->setActiveOnly(false);
        $products->setShowOutOfStock(true);


        if ($this->get('ccm_order_by')) {
            $products->setSortBy($this->get('ccm_order_by'));
            $products->setSortByDirection($this->get('ccm_order_by_direction'));
        } else {
            $products->setSortBy('date');
            $products->setSortByDirection('desc');
        }


        if ($this->get('keywords')) {
            $products->setSearch($this->get('keywords'));
        }

        $productList = $products;
        $paginator = $products->getPagination();
        $pagination = $paginator->renderDefaultView();
       	$products = $paginator->getCurrentPageResults();
	
	
	

	
		$productsArray = array();
	
		if(count($products)>0) {
		
		
		foreach ( $products as $key=>$product ) {
			
			
		$pPriceSuggestions = '1';
			$noneselection = '1';
			
			$productID = $product->getID();
			
			  $productName = $product->getName();
			  
			  $getSKU = $product->getSKU();
			  
			 if ($product->isActive()) {
					$active ='Active';
				} else {
					$active ='Inactive';
                    }
			
			if ($product->hasVariations()) {
					$StockLevel = '1';
					} else {
						$stockLevel = '0';
					}
			$basicPrice = $product->getFormattedPrice();

			$isUnlimited = $product->isUnlimited();

			if ($product->isFeatured()) {
				   $isFeatured = 'Featured';
				} else {
					$isFeatured = 'Not Featured';
				}

			$salePrice = '$'.$product->getPrice();
			
			$minimumPrice = $product->getPriceMinimum(); 
			
			$maximumPrice = $product->getPriceMaximum(); 
			
			if($product->isTaxable()==1){
			
				$isTaxable = 'Yes';
				
			}elseif($product->isTaxable()==0){
				
				$isTaxable = 'No';
			}
			
			$getTaxClassID = $product->getTaxClassID();
			
			$pNoQty = $product->getQty();
			
			$allowBackOrders = $product->allowBackOrders();
			
			
			if($product->allowQuantity()==1){
			
				$OfferQuantitySelection = 'Only allow one of this product in a cart';
				
			}elseif($product->allowQuantity()==0){
				
				$OfferQuantitySelection = 'Yes';
			}
			
			
			$getDateAdded = $dh->formatCustom('Y-m-d', $product->getDateAdded());
			
			$pDateAdded_h = $dh->formatCustom('h', $product->getDateAdded());
			
			$pDateAdded_m = $dh->formatCustom('i', $product->getDateAdded());
			
			$pDateAdded_a = $dh->formatCustom('a', $product->getDateAdded());
			
			
			$shortDescription = $product->getDesc();
			
			$detailDescription = $product->getDetail();
			
			
			 $locationPages = $product->getLocationPages();
			 $cIDs=array();
			 if (!empty($locationPages)) {
				
                            foreach ($locationPages as $location) {
                                if ($location) {
                                    $page = \Page::getByID($location->getCollectionID());
                                    $cIDs[] = $location->getCollectionID();
                                }
                            }
                        }
			
			$isShippable = $product->isShippable();
			
			$getWeight = $product->getWeight();
			
			$getNumberItems = $product->getNumberItems();
			
			$getLength = $product->getLength();
			
			$getWidth = $product->getWidth(); 
			
			$getHeight = $product->getHeight();
			
			if(is_object($product->getImageObj())){
			
				$getImagePath = $product->getImageObj()->getRelativePath();
			
			}
			if(sizeof($product->getimagesobjects())>0){
			$additionlImage = array();
			foreach ($product->getimagesobjects() as $file) {
                        if ($file) {
							
						$additionlImage[] = $file->getRelativePath();
                            
                        }
                    }
				$additionlImage = implode(', ',$additionlImage);
			
			}
			
			
			//get variations
			
			$pID = $product->getID();
			$product = StoreProduct::getByID($pID);
			
			if ($product) {
            if ($product->hasVariations()) {
                $variations = StoreProductVariation::getVariationsForProduct($product);

                $variationLookup = array();

                if (!empty($variations)) {
                    foreach ($variations as $variation) {
                        // returned pre-sorted
                        $ids = $variation->getOptionItemIDs();
                        $variationLookup[implode('_', $ids)] = $variation;
                    }
                }

                $product->setInitialVariation();
                $variationLookup= $variationLookup;
            }

        }
		
		//print_r($variationLookup);
		//die;
			
		$varationData = array();
			$c = 1;
			$count = count($variationLookup);
			if($count>0){
			
            foreach($variationLookup as $key=>$variation) {
                $product->setVariation($variation);

				
				if($c == $count){
				$hf = '';
				}else{
				$hf = '||';	
				}
				$label = Options::getOptionLabel($key);
				
				$varationData[$key] =  $label.'-'.$product->getFormattedOriginalPrice().'-'.$product->getSKU().$hf;		

             $c++;  
            }
			}
			
			
			
			
			$productsArray['product-'.$key]['pName']=$productName;
			$productsArray['product-'.$key]['pSKU']=$getSKU;
			$productsArray['product-'.$key]['pActive']=htmlspecialchars($active,ENT_QUOTES,'UTF-8');
			$productsArray['product-'.$key]['pFeatured']=htmlspecialchars($isFeatured,ENT_QUOTES,'UTF-8');
			$productsArray['product-'.$key]['pPrice']=htmlspecialchars($basicPrice,ENT_QUOTES,'UTF-8');
			$productsArray['product-'.$key]['pSalePrice']=htmlspecialchars($salePrice,ENT_QUOTES,'UTF-8');
			$productsArray['product-'.$key]['pPriceSuggestions']=htmlspecialchars($product->prRequiresLoginToPurchase,ENT_QUOTES,'UTF-8');
			$productsArray['product-'.$key]['pPriceMinimum']=htmlspecialchars($minimumPrice,ENT_QUOTES,'UTF-8');
			$productsArray['product-'.$key]['pPriceMaximum']=htmlspecialchars($maximumPrice,ENT_QUOTES,'UTF-8');
			$productsArray['product-'.$key]['pTaxable']=htmlspecialchars($isTaxable,ENT_QUOTES,'UTF-8');
			$productsArray['product-'.$key]['pTaxClass']=htmlspecialchars($getTaxClassID,ENT_QUOTES,'UTF-8');
			$productsArray['product-'.$key]['pQtyUnlim']=htmlspecialchars($isUnlimited,ENT_QUOTES,'UTF-8');
			$productsArray['product-'.$key]['pNoQty']=htmlspecialchars($pNoQty,ENT_QUOTES,'UTF-8');
			$productsArray['product-'.$key]['pDateAdded_dt']=htmlspecialchars($getDateAdded,ENT_QUOTES,'UTF-8');
			$productsArray['product-'.$key]['pDateAdded_h']=htmlspecialchars($pDateAdded_h,ENT_QUOTES,'UTF-8');
			$productsArray['product-'.$key]['pDateAdded_m']=htmlspecialchars($pDateAdded_m,ENT_QUOTES,'UTF-8');
			$productsArray['product-'.$key]['pDateAdded_a']=htmlspecialchars(strtoupper($pDateAdded_a),ENT_QUOTES,'UTF-8');
			$productsArray['product-'.$key]['pDesc']=htmlspecialchars($shortDescription,ENT_QUOTES,'UTF-8');;
			$productsArray['product-'.$key]['pDetail']=htmlspecialchars($detailDescription,ENT_QUOTES,'UTF-8');
			$productsArray['product-'.$key]['cID']=htmlspecialchars(implode('||', $cIDs),ENT_QUOTES,'UTF-8');
			$productsArray['product-'.$key]['noneselection']=htmlspecialchars($noneselection,ENT_QUOTES,'UTF-8');
			$productsArray['product-'.$key]['pShippable']=htmlspecialchars($isShippable,ENT_QUOTES,'UTF-8');
			$productsArray['product-'.$key]['pWeight']=htmlspecialchars($getWeight,ENT_QUOTES,'UTF-8');
			$productsArray['product-'.$key]['pNumberItems']=htmlspecialchars($getNumberItems,ENT_QUOTES,'UTF-8');
			$productsArray['product-'.$key]['pLength']=htmlspecialchars($getLength,ENT_QUOTES,'UTF-8');
			$productsArray['product-'.$key]['pWidth']=htmlspecialchars($getWidth,ENT_QUOTES,'UTF-8');
			$productsArray['product-'.$key]['pHeight']=htmlspecialchars($getHeight,ENT_QUOTES,'UTF-8');
			$productsArray['product-'.$key]['PrimaryProductImage']=htmlspecialchars($getImagePath,ENT_QUOTES,'UTF-8');
			$productsArray['product-'.$key]['AdditionalImages']=htmlspecialchars($additionlImage,ENT_QUOTES,'UTF-8');
			
			
			
			$i++;
			
		}
		
	
		}
	//print_r($productsArray);
		//die;	
		


//creating object of SimpleXMLElement
$xml_user_info = new SimpleXMLElement("<?xml version=\"1.0\"?><products></products>");
//print_r($productsArray);die;
//function call to convert array to xml
$this->array_to_xml($productsArray,$xml_user_info);

//saving generated xml file
$xml_file = $xml_user_info->asXML('products.xml');
//success and error message based on xml creation
if($xml_file){
    echo 'XML file have been generated successfully.';
}else{
    echo 'XML file generation error.';
}	
		
function array_to_xml($array, &$xml_user_info) {
    foreach($array as $key => $value) {
        if(is_array($value)) {
            if(!is_numeric($key)){
                $subnode = $xml_user_info->addChild("$key");
                $this->array_to_xml($value, $subnode);
            }else{
                $subnode = $xml_user_info->addChild("item$key");
                $this->array_to_xml($value, $subnode);
            }
        }else {
            $xml_user_info->addChild("$key",htmlspecialchars("$value"));
        }
    }
}	
	
	
