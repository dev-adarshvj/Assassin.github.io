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
			
			
			
			$page = Page::getByID($product->getPageID());
			
			$categoryPage = Page::getByID($page->getCollectionParentID());
			
			
			
			
			if ( !$headerDisplayed ) {
			$key = array(
				t('id'),
				t('Name'),
				t('SKU'),
                t('Active'),
				t('Is Featured'),
                t('Basic Price'),
				t('Sale Price'),
				t('Minimum Price'),
				t('Maximum Price'),
				t('Is Taxable'),
				t('Tax ClassID'),
				t('Unlimited'),
				t('Stock Level'),
				t('Allow Back Orders'),
				t('Offer Quantity Selection'),
				t('Date Added'),
				t('Short Description'),
				t('Detail Description'),
				t('Product is Shippable'),
				t('Weight'),
				t('Number Of Items'),
				t('Length'),
				t('Width'),
				t('Height'),
				t('Primary Product Image'),
				t('Additional Images'),
				t('Option'),
				t('color_code_jewel'),
				t('color_code_vibrant'),
				t('related_products'),
				t('pdf_download'),
				t('youtube_video_url'),
				t('color_code_classic'),
				t('imprint_product'),
				t('enable_saved_designs'),
				t('product_color_display'),
				t('select_pdf'),
				t('category')
				
            );
            
            
            
            
            
				fputcsv($fh,$key);
				$headerDisplayed = true;
			}
			
			$productID = $product->getID();
			
			$productName = $product->getName();
			
			$getSKU = $product->getSKU();
			
			//$active = $product->isActive();
			
			if ($product->isActive()) {
					$active = 1;
				} else {
					$active = 0;
                    }
			 
			/* if ($product->isActive()) {
					$active ='Active';
				} else {
					$active ='Inactive';
                    }
			*/
			
		if ($product->hasVariations()) {
					//$StockLevel = 'Multiple';
					$StockLevel = 1;
					} else {
						//$stockLevel = 'Unlimited';
						$stockLevel = 0;
					}
		$basicPrice = $product->getPrice();

		if ($product->isFeatured()) {
				   //$isFeatured = 'Featured';
				     $isFeatured = 1;
				   
				   
				} else {
					//$isFeatured = 'Not Featured';
					 $isFeatured = 0;
				}


			$salePrice = $product->getSalePrice();
			
			$minimumPrice = $product->getPriceMinimum(); 
			
			$maximumPrice = $product->getPriceMaximum(); 
			
			$isTaxable = $product->isTaxable();
			
			/*if($product->isTaxable()==1){
			
				$isTaxable = 'Yes';
				
			}elseif($product->isTaxable()==0){
				
				$isTaxable = 'No';
			}*/
			
			$getTaxClassID = $product->getTaxClassID();
			
			$stockLevel = $product->getQty();
			
			$isUnlimited = $product->isUnlimited();
			
			
			$allowBackOrders = $product->allowBackOrders();
			
			
			if($product->allowQuantity()==1){
			
				//$OfferQuantitySelection = 'Only allow one of this product in a cart';
				$OfferQuantitySelection = 1;
				
				
			}elseif($product->allowQuantity()==0){
				
				//$OfferQuantitySelection = 'Yes';
				$OfferQuantitySelection = 0;
			}
			
			$getDateAdded = $dh->formatDateTime($product->getDateAdded());
			
			$shortDescription = $product->getDesc();
			
			$detailDescription = $product->getDetail();
			
			
			
			$isShippable = $product->isShippable();
				
			
			/*if($product->isShippable()==0){
			
				$isShippable = 'No';
				
			}elseif($product->isShippable()==1){
				
				$isShippable = 'Yes';
			}*/
			
			$getWeight = $product->getWeight();
			
			$getNumberItems = $product->getNumberItems();
			
			$getLength = $product->getLength();
			
			$getWidth = $product->getWidth(); 
			
			$getHeight = $product->getHeight();
			$getImagePath = '';
			if(is_object($product->getImageObj())){
			
				$getImagePath = BASE_URL.$product->getImageObj()->getRelativePath();
			
			}
			$additionlImage = array();
			if(sizeof($product->getimagesobjects())>0){
			
			foreach ($product->getimagesobjects() as $file) {
                        if ($file) {
							
						$additionlImage[] = BASE_URL.$file->getRelativePath();
                            
                        }
                    }
				
			
			}
			
			$additionlImage = implode(', ',$additionlImage);
			
			//get variations
			
			$pID = $product->getID();
			$product = StoreProduct::getByID($pID);
			 $variationLookup = array();
			if ($product) {
            if ($product->hasVariations()) {
                $variations = StoreProductVariation::getVariationsForProduct($product);

               

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
			
			
			/* Fetching additional data */
			$color_code_jewel = $product->getAttribute('color_code_jewel');
			$color_code_vibrant = $product->getAttribute('color_code_vibrant');
			$related_products = $product->getAttribute('related_products');
			
			$pdf_download = $product->getAttribute('pdf_download');
			$youtube_video_url = $product->getAttribute('youtube_video_url');
			$color_code_classic = $product->getAttribute('color_code_classic');
			
			$imprint_product = $product->getAttribute('imprint_product');
			$enable_saved_designs = $product->getAttribute('enable_saved_designs');
			
			$product_color_display = $product->getAttribute('product_color_display');
			
			$select_pdf = $product->getAttribute('select_pdf');
			
			
			
				
			
			
			$dataContent = array(
				$productID,
				$productName,
				$getSKU,
				$active,
				$isFeatured,
				$basicPrice,
				$salePrice,
				$minimumPrice,
				$maximumPrice,
				$isTaxable,
				$getTaxClassID,
				$isUnlimited,
				$stockLevel,
				$allowBackOrders,
				$OfferQuantitySelection,
				$getDateAdded,
				$shortDescription,
				$detailDescription,
				$isShippable,
				$getWeight,
				$getNumberItems,
				$getLength,
				$getWidth,
				$getHeight,
				$getImagePath,
				$additionlImage,
				implode('',$varationData),
				$color_code_jewel,
				$color_code_vibrant,
				$related_products,
				$pdf_download,
				$youtube_video_url,
				$color_code_classic,
				$imprint_product,
				$enable_saved_designs,
				$product_color_display,
				$select_pdf,
				$categoryPage->getCollectionName()
				
				
            );
			
			$i++;
			fputcsv($fh,$dataContent);
		}
		fclose($fh);
		exit;	
		die;
	
		}
		
		
		
		
	
	
	
