<?php  
defined('C5_EXECUTE') or die("Access Denied.");
use \Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductList as StoreProductList;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductVariation\ProductVariation as StoreProductVariation;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Product\Product as StoreProduct;
use \Concrete\Package\StoreExportImport\Src\Options;

		
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
	
	
	
	
		if(count($products)>0) {
		
		
		$date = date('Ymd');	
		$fileName = 'product_details.csv';
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header('Content-Description: File Transfer');
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename={$fileName}");
		header("Expires: 0");
		header("Pragma: public");
		$fh = @fopen( 'php://output', 'w' );
		$headerDisplayed = false;
		$i=1;
		
		
		
		foreach ( $products as $product ) {
			
			
			if ( !$headerDisplayed ) {
			$key = array(
				t('pID'),
				t('pName'),
				t('pSKU'),
				t('pActive'),
				t('pFeatured'),
				t('pPrice'),
				t('pSalePrice'),
				t('pPriceSuggestions'),
				t('pPriceMinimum'),
				t('pPriceMaximum'),
				
				t('pTaxable'),
				t('pTaxClass'),
				
				t('pQtyUnlim'),
				t('pNoQty'),
				
				
				//t('Offer Quantity Selection"'),
				t('pDateAdded_dt'),
				t('pDateAdded_h'),
				t('pDateAdded_m'),
				t('pDateAdded_a'),
				t('pDesc'),
				t('pDetail'),
				t('cID'),
				t('noneselection'),
				t('pShippable'),
				t('pWeight'),
				t('pNumberItems'),
				t('pLength'),
				t('pWidth'),
				t('pHeight'),
				t('PrimaryProductImage'),
				t('AdditionalImages'),
//				t('Option'),
				t('poName'),
				t('poHandle')
				
				
            );
			
				
			
			
			
			
			
				fputcsv($fh,$key);
				$headerDisplayed = true;
			}
			
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
			
			$options = $product->getOptions();
			
			
			
			$getName = array();
			$getHandle = array();
			 if($options) {
                            foreach ($options as $option) {

                            $type = $option->getType();
                            $handle = $option->getHandle();
                            $required = $option->getRequired();


            				 $getName[] = $option->getName();
							 $getHandle[] = $option->getHandle();
			  
			          
							}
			 }
			//print_r($getHandle);
			//die;
			//////////////
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
			$dataContent = array(
				$productID,
				$productName,
				$getSKU,
				$active,
				$isFeatured,
				$basicPrice,
				$salePrice,
				$pPriceSuggestions,
				$minimumPrice,
				$maximumPrice,
				$isTaxable,
				$getTaxClassID,
				$isUnlimited,
				$pNoQty,
//$allowBackOrders,
//$OfferQuantitySelection,
				$getDateAdded,
				$pDateAdded_h,
				$pDateAdded_m,
				strtoupper($pDateAdded_a),
				$shortDescription,
				$detailDescription,
				implode('||', $cIDs),
				$noneselection,
				$isShippable,
				$getWeight,
				$getNumberItems,
				$getLength,
				$getWidth,
				$getHeight,
				$getImagePath,
				$additionlImage,
				implode('||',$getName),
				implode('||',$getHandle)
//				implode('',$varationData)
				
				
            );
			
			$i++;
			fputcsv($fh,$dataContent);
		}
		fclose($fh);
		exit;	
		die;
	
		}
		
		
		
		
	
	
	
