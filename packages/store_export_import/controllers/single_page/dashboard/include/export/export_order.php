<?php  
defined('C5_EXECUTE') or die("Access Denied.");
use Concrete\Package\CommunityStore\Src\CommunityStore\Order\OrderStatus\OrderStatus as StoreOrderStatus;
use Concrete\Package\CommunityStore\Src\CommunityStore\Order\OrderList as StoreOrderList;
use Concrete\Package\CommunityStore\Src\CommunityStore\Order\Order as StoreOrder;
use Concrete\Package\CommunityStore\Src\Attribute\Key\StoreOrderKey as StoreOrderKey;
use \Concrete\Core\Page\Controller\DashboardPageController;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Utilities\Price as Price;

use Application\Src\Distributor as Distributor;

		$dh = Core::make('helper/date');
		$orderList = new StoreOrderList();
        if ($this->get('keywords')) {
            $orderList->setSearch($this->get('keywords'));
        }
        if ($status) {
            $orderList->setStatus($status);
        }
        $orderList->setItemsPerPage(10000);
        $paginator = $orderList->getPagination();
        $pagination = $paginator->renderDefaultView();
        $orderList = $paginator->getCurrentPageResults();
		
		

		$date = date('Ymd');	
		$fileName = 'order_details.csv';
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header('Content-Description: File Transfer');
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename={$fileName}");
		header("Expires: 0");
		header("Pragma: public");
		$fh = @fopen( 'php://output', 'w' );
		$headerDisplayed = false;
		$i=1;
		foreach ( $orderList as $order ) {
			
		//$altre = trim(preg_replace('/\s\s+/', ' ', $data['altre_informazioni']));
			
			
			 
			//unset($data['data']);
			
			if ( !$headerDisplayed ) {
			$key = array(
				t('Order'),
				t('Customer Name'),
				t('Distributor'),
				t('Order Date'),
              //  t('Payment Status'),
				t('Fulfilment'),
				t('Customer Email'),
				t('Customer Phone'),
				t('VAT'),
				t('Billing First Name'),
				t('Billing Last Name'),
				t('Address 1'),
				t('Address 2'),
				t('City'),
				t('State Province'),
				t('Postal Code'),
				//t('Billing Address'),
				//t('Shipping Address'),
				t('Product Name'),
				t('Variations'),
				t('Price'),
				t('Quantity'),
				t('Subtotal'),
				t('Grand Total'),
				t('Payment Method')
            );
            
          
            
            
				fputcsv($fh,$key);
				$headerDisplayed = true;
			}
			
			
	
                //orderID
				$orderID = $order->getOrderID();
				// Customer Name 
				$last = $order->getAttribute('billing_last_name');
				$first = $order->getAttribute('billing_first_name');

                   if ($last || $first ) {
                    //$CustomerName = $last.", ".$first;
					$CustomerName = $first;
                   } else {
                    $CustomerName = 'Not found';
                   }    	
			
				//orderDate
				$orderDate = $dh->formatDateTime($order->getOrderDate());
				//$orderDate = $this->replaceTO($orderDate,',',' ');
				//price 
				//$totalPrice = Price::format($order->getTotal());
				
				//paymentMethod
				 	$refunded = false;
                        $paid = true;
                        
                        //$refunded = $order->getRefunded();
                        //$paid = $order->getPaid();

                        if ($refunded) {
                            $payment = 'Refunded';
                        } elseif ($paid) {
                             $payment =  'Paid';
                        } elseif ($order->getTotal() > 0) {
                             $payment =  'Unpaid';
                        } else {
                             $payment =  'Free Order';
                        }
				
				//status
				$status = t(ucwords($order->getStatus()));
				//orderEmail
				$orderemail = $order->getAttribute("email");
				//phone
				$phone = $order->getAttribute("billing_phone");
				//VAT
				$vat_number = $order->getAttribute("vat_number");
                    if (Config::get('community_store.vat_number') && $vat_number) { 
                       $VAT = $vat_number;
                     } 
				//billingAddress
				$billingaddress = $order->getAttributeValueObject(StoreOrderKey::getByHandle('billing_address'));
                        if ($billingaddress) {
                            $BillingAddr = $billingaddress->getValue('displaySanitized', 'display');
							$BillingAddr = preg_replace("#<br />#", " ", $BillingAddr);
							$BillingAddr = $this->trimTO($this->replaceTO($BillingAddr,',',' '));
							
                        }
						
				//shippingAddress
                $shippingAddress = $order->getAttributeValueObject(StoreOrderKey::getByHandle('shipping_address'));
                    if ($shippingAddress) { 
                        //$order->getAttribute("shipping_first_name") . " " . $order->getAttribute("shipping_last_name") ;
                       	$shippingAddr = $shippingAddress->getValue('displaySanitized', 'display'); 
						$shippingAddr = preg_replace("#<br />#", " ", $shippingAddr);
						$shippingAddr = $this->trimTO($this->replaceTO($shippingAddr,',',' '));
						
                       
                    } 
			
				$grandTotal = Price::format($order->getTotal());
			
				$paymentMethod = $order->getPaymentMethodName();
				
			  $items = $order->getOrderItems();
                if ($items) {
					
					$oKeyValue = array(); 
					$productName = array();
					$Price = array();
					$Quantity = array();
					$SubTotal = array();
                    foreach ($items as $item) {
						//productName
                       $productName[] = $item->getProductName().'('. $item->getSKU().')';
					  	$getSKU[] = $item->getSKU();
                        $options = $item->getProductOptions();
                               
							   foreach($options as $option){
								$oKeyValue[] =	$option['oioKey'].':'.$option['oioValue'];
                                }
							
							//Price	
                           $Price[] = Price::format($item->getPricePaid());
						   //Quantity
                           $Quantity[] = $item->getQty();
						   //SubTotal
                           $SubTotal[] = Price::format($item->getSubTotal());
                        
                    }
                }
			
			$distributor = Distributor::get($order->getOrderID());
			
			$billing_first_name = $order->getAttribute("billing_first_name");
			$billing_last_name = $order->getAttribute("billing_last_name");
			$address1 = $order->getAttribute("billing_address")->address1;
			$address2 = $order->getAttribute("billing_address")->address2;
			$city = $order->getAttribute("billing_address")->city;
			$state_province = $order->getAttribute("billing_address")->state_province;
			$postal_code = $order->getAttribute("billing_address")->postal_code;
			
			
									
			$dataContent = array(
				$orderID,
				$CustomerName,
				$distributor['distributorName'],
				$orderDate,
				//$totalPrice,
			//	$payment,
				$status,
				$orderemail,
				$phone,
				$VAT,
				$billing_first_name,
				$billing_last_name,
				$address1,
				$address2,
				$city,
				$state_province,
				$postal_code,
				//$BillingAddr,
				//$shippingAddr,
				implode('||',$productName),
				implode('||',$oKeyValue),
				implode('||',$Price),
				implode('||',$Quantity),
				implode('||',$SubTotal),
				$grandTotal,
				$paymentMethod
				
            );
			
			$i++;
			fputcsv($fh,$dataContent);
		}
		fclose($fh);
		exit;	
		die;
	
	
	
