<?php
     
    class TSM_OSRCustomShipping_Model_Osrcustomfreightshipping
        extends Mage_Shipping_Model_Carrier_Abstract
        implements Mage_Shipping_Model_Carrier_Interface
    {
     
        protected $_code = 'osrcustomfreightshipping';
    	protected $_isFixed = true;
     
        /**
         * Enter description here...
         *
         * @param Mage_Shipping_Model_Rate_Request $data
         * @return Mage_Shipping_Model_Rate_Result
         */
	public function collectRates(Mage_Shipping_Model_Rate_Request $request){
		Mage::log('In osrcustomfreightshipping',null,'tsmFreightLogFile.log');
           	if (!$this->getConfigFlag('active')) {
    			Mage::log('osrcustomfreightshipping is not active: ' . $this->getConfigFlag('active'),null,'tsmFreightLogFile.log');
                	return false;
            	}
    		Mage::log('osrcustomfreightshipping is active: ' . $this->getConfigFlag('active'),null,'tsmFreightLogFile.log');
     
            	$freeBoxes = 0;
            	if ($request->getAllItems()) {
                	foreach ($request->getAllItems() as $item) {
                    		if ($item->getFreeShipping() && !$item->getProduct()->isVirtual()) {
                        		$freeBoxes+=$item->getQty();
                    		}
                	}
            	}
            	$this->setFreeBoxes($freeBoxes);
            
            	$result = Mage::getModel('shipping/rate_result');    	    	
    	    	$allItems = $request->getAllItems();
    	    	$numberOfFreightProducts = 0;
    	    
    	    	foreach($allItems as $item){    		
			$productId = Mage::getModel('catalog/product')->getIdBySku($item->getSku());
			$isFreight = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'atv_is_freight', Mage::app()->getStore());
			if($isFreight){
    				Mage::log('Prodcut Id: ' . $productId . ' is freight.',null,'tsmOSRFreeLogFile.log');
				$numberOfFreightProducts =+ $item->getQty();
    			}
    	    	}
    	    
    		Mage::log('Number of products that are freight: ' . $numberOfFreightProducts,null,'tsmFreightLogFile.log');
		if($numberOfFreightProducts == 0){
    			Mage::log('There are no freight products in cart.',null,'tsmFreightLogFile.log');
			return false;
    		}else{
    	    		$shippingPrice = $this->getConfigData('freight_rate') * $numberOfFreightProducts;
    		}
  	
            
		Mage::log('Shipping price ' . $shippingPrice,null,'tsmFreightLogFile.log');
     
        	if ($shippingPrice !== false) {
                	$method = Mage::getModel('shipping/rate_result_method');
     
                	$method->setCarrier('osrcustomfreightshipping');
                	$method->setCarrierTitle($this->getConfigData('title'));
     
                	$method->setMethod('osrcustomfreightshipping');
                	$method->setMethodTitle($this->getConfigData('name'));
     
                	if ($request->getFreeShipping() === true || $request->getPackageQty() == $this->getFreeBoxes()) {
                    		$shippingPrice = '0.00';
                	}
     
     
                	$method->setPrice($shippingPrice);
                	$method->setCost($shippingPrice);
     
                	$result->append($method);
            	}
     
            	return $result;
	}
     
        public function getAllowedMethods()
        {
            return array('osrcustomfreightshipping'=>$this->getConfigData('name'));
        }
     
    }
    ?>