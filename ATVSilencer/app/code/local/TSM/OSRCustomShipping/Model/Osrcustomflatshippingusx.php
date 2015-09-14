<?php
     
    class TSM_OSRCustomShipping_Model_Osrcustomflatshippingusx
        extends Mage_Shipping_Model_Carrier_Abstract
        implements Mage_Shipping_Model_Carrier_Interface
    {
     
        protected $_code = 'osrcustomflatshippingusx';
    	protected $_isFixed = true;
     
        /**
         * Enter description here...
         *
         * @param Mage_Shipping_Model_Rate_Request $data
         * @return Mage_Shipping_Model_Rate_Result
         */
        public function collectRates(Mage_Shipping_Model_Rate_Request $request)
        {
    		Mage::log('In osrcustomfreeshipping',null,'tsmOsrcustomflatshippingusxLogFile.log');
        
           if (!$this->getConfigFlag('active')) {
    		Mage::log('is not active: ' . $this->getConfigFlag('active'),null,'tsmOsrcustomflatshippingusxLogFile.log');
                return false;
            }
    		Mage::log('is active: ' . $this->getConfigFlag('active'),null,'tsmOsrcustomflatshippingusxLogFile.log');
     
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
    	   	//Mage::log('items: ',null,'tsmOsrcustomflatshippingusxLogFile.log');
    	   $surCharge = 0;
    	    foreach($allItems as $item){    		
		$productId = Mage::getModel('catalog/product')->getIdBySku($item->getSku());
		$isFreight = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'atv_is_freight', Mage::app()->getStore());
    		$surCharge = $surCharge + Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'atv_shipping_surcharge', Mage::app()->getStore());
    	   	Mage::log('surchage added: ' . $surCharge,null,'tsmOsrcustomflatshippingusxLogFile.log');
    	   	Mage::log('to be added: ' . Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'atv_shipping_surcharge', Mage::app()->getStore()),null,'tsmOsrcustomflatshippingusxLogFile.log');
		if($isFreight){
    			Mage::log('Cart contains products that require freight so no free shipping. Prodcut Id: ' . $productId,null,'tsmOsrcustomflatshippingusxLogFile.log');
    			
			//return false;
    		}
    	    }    	    
    	   	Mage::log('surcharge total: ' . $surCharge,null,'tsmOsrcustomflatshippingusxLogFile.log');
    	    if($request->getPackageValue() < $this->getConfigData('first_up_to')){
    		Mage::log('Cart Total Amount is less than the fist up to. Cart total: ' . $request->getPackageValue(),null,'tsmOsrcustomflatshippingusxLogxFile.log');
    		$shippingPrice = $this->getConfigData('first_rate');
    	    }else if($request->getPackageValue() < $this->getConfigData('second_up_to')){
    		Mage::log('Cart Total Amount is less than the Second up to. Cart total: ' . $request->getPackageValue(),null,'tsmOsrcustomflatshippingusxLogFile.log');
    		$shippingPrice = $this->getConfigData('second_rate');
    	    }else{
    		Mage::log('Cart Total Amount is less than the third up to. Cart total: ' . $request->getPackageValue(),null,'tsmOsrcustomflatshippingusxLogFile.log');
    		$shippingPrice = $this->getConfigData('third_rate');
    	    }
     
    	    $hasSurCharge = false;
    	    //$surCharge = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'atv_shipping_surcharge', Mage::app()->getStore());
     	    if($surCharge > 0){
    		Mage::log('Shipping Charge Greater than 0: ' . $surCharge,null,'tsmOsrcustomflatshippingusxLogFile.log');
    		$hasSurCharge = true;
    		$shippingPrice = $shippingPrice + $surCharge;
    		Mage::log('Shipping Charge + shipping Price: ' . $shippingPrice,null,'tsmOsrcustomflatshippingusxLogFile.log');
     	    }else{
    		Mage::log('Shipping Charge Greater equal to 0: ' . $surCharge,null,'tsmOsrcustomflatshippingusxLogFile.log');
     	    }
            if ($shippingPrice !== false) {
                $method = Mage::getModel('shipping/rate_result_method');
     
                $method->setCarrier('osrcustomflatshippingusx');
                if($hasSurCharge){
    		//Mage::log('Is true',null,'tsmOsrcustomflatshippingusxLogFile.log');
                	$method->setCarrierTitle('Shipping Surcharge');
                }else{
    		//Mage::log('Is false',null,'tsmOsrcustomflatshippingusxLogFile.log');
                	$method->setCarrierTitle($this->getConfigData('title'));
                }
     
                $method->setMethod('osrcustomflatshippingusx');
                if($hasSurCharge){
    		Mage::log('Is true',null,'tsmOsrcustomflatshippingusxLogFile.log');
                	 $method->setMethodTitle($this->getConfigData('name') . ' + $' . $surCharge . ' Shipping Surcharge - ');
                }else{
    		Mage::log('Is false',null,'tsmOsrcustomflatshippingusxLogFile.log');
                	 $method->setMethodTitle($this->getConfigData('name'));
                }
     
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
            return array('osrcustomflatshippingusx'=>$this->getConfigData('name'));
        }
     
    }
    ?>