<?php
     
    class TSM_OSRCustomShipping_Model_Osrcustomfreeshipping
        extends Mage_Shipping_Model_Carrier_Abstract
        implements Mage_Shipping_Model_Carrier_Interface
    {
     
        protected $_code = 'osrcustomfreeshipping';
    	protected $_isFixed = true;
     
        /**
         * Enter description here...
         *
         * @param Mage_Shipping_Model_Rate_Request $data
         * @return Mage_Shipping_Model_Rate_Result
         */
        public function collectRates(Mage_Shipping_Model_Rate_Request $request)
        {
    		Mage::log('In osrcustomfreeshipping',null,'tsmOSRFreeLogFile.log');
        
           if (!$this->getConfigFlag('active')) {
    		Mage::log('is not active: ' . $this->getConfigFlag('active'),null,'tsmOSRFreeLogFile.log');
                return false;
            }
    		Mage::log('is active: ' . $this->getConfigFlag('active'),null,'tsmOSRFreeLogFile.log');
    		/*
           if (!$this->getConfigFlag('active')) {
    		Mage::log('is not active: ' . $this->getConfigFlag('active'),null,'tsmOSRFreeLogFile.log');
                return false;
            }*/
     
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
    	   	Mage::log('items: ',null,'tsmOSRFreeLogFile.log');
    	    foreach($allItems as $item){    		
		$productId = Mage::getModel('catalog/product')->getIdBySku($item->getSku());
		$isFreight = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'atv_is_freight', Mage::app()->getStore());
		if($isFreight){
    			Mage::log('Cart contains products that require freight so no free shipping. Prodcut Id: ' . $productId,null,'tsmOSRFreeLogFile.log');
			return false;
    		}
    	    }
    	    
    	    if($this->getConfigData('minium_amount') > $request->getPackageValue()){
    		Mage::log('Cart Total Amount is less than the minimuim allowed for shipping. Cart total: ' . $request->getPackageValue(),null,'tsmOSRFreeLogFile.log');
    	    $hasSurCharge = false;
    	    $surCharge = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'atv_shipping_surcharge', Mage::app()->getStore());
     	    	if($surCharge > 0){
    			Mage::log('Shipping Charge Greater than 0: ' . surCharge,null,'tsmOSRFreeLogFile.log');
    			$hasSurCharge = true;
    			$shippingPrice = $shippingPrice + $surCharge;
    		
     	    	}else{
    			Mage::log('Shipping Charge Greater equal to 0: ' . surCharge,null,'tsmOSRFreeLogFile.log');
    	    		return false;
     	    	}
    	    }else{
    		Mage::log('Cart Total Amount is greater than the minimuim allowed for free shipping. Cart total: ' . $request->getPackageValue(),null,'tsmOSRFreeLogFile.log');
    	    	$shippingPrice = 0;
    	    }
    	    $hasSurCharge = false;
    	    $surCharge = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'atv_shipping_surcharge', Mage::app()->getStore());
     	    if($surCharge > 0){
    		Mage::log('Shipping Charge Greater than 0: ' . surCharge,null,'tsmOSRFreeLogFile.log');
    		$hasSurCharge = true;
    		$shippingPrice = $shippingPrice + $surCharge;
     	    }else{
    		Mage::log('Shipping Charge Greater equal to 0: ' . surCharge,null,'tsmOSRFreeLogFile.log');
     	    }
            if ($shippingPrice !== false) {
                $method = Mage::getModel('shipping/rate_result_method');
     
                $method->setCarrier('osrcustomfreeshipping');
                if($hasSurCharge){
                	$method->setCarrierTitle('Shipping Surcharge');
                }else{
                	$method->setCarrierTitle($this->getConfigData('title'));
                }
     
                $method->setMethod('osrcustomfreeshipping');
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
            return array('osrcustomfreeshipping'=>$this->getConfigData('name'));
        }
     
    }
    ?>