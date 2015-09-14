<?php
class TSM_OSRNoFreightShipped_Model_Observer{
	/**
	* Exports an order after it is placed
     	*
     	* @param Varien_Event_Observer $observer observer object
     	*
     	* @return boolean
     	*/
    	public function noFreightShipping(Varien_Event_Observer $observer){
    		//Mage::log('Function noFreightShipping: Beginning.',null,'noFreightShippingLogFile.log');
    		$active = Mage::app()->getStore()->getConfig('carriers/osrcheckforspecialorder/active');
    		if(!$active){
    			//Mage::log('Not Active: ' . $active,null,'noFreightShippingLogFile.log');
    			return false;
    		}
    		//Mage::log('	getting Quote.',null,'noFreightShippingLogFile.log');
    		$quote = $observer->getEvent()->getQuote();
    		//Mage::log('	retrieved Quote.',null,'noFreightShippingLogFile.log');
    		//Mage::log('	getting items.',null,'noFreightShippingLogFile.log');
    		$allItems = $quote->getAllItems();
    		//Mage::log('	Items retrieved.',null,'noFreightShippingLogFile.log');
    		foreach($allItems as $item){    		
			$productId = Mage::getModel('catalog/product')->getIdBySku($item->getSku());
			$isFreight = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'atv_is_freight', Mage::app()->getStore());
			if($isFreight){
    				//Mage::log('Product requires freight shipping. Product Id: ' . $productId,null,'noFreightShippingLogFile.log');
				//Mage::getSingleton('checkout/session')->addError('Product requires freight shipping. To order this item, please call (866)-EXHAUST (394-2878), or email info@gensilencer.com for a quote. ');
				Mage::getSingleton('checkout/session')->addError(Mage::app()->getStore()->getConfig('carriers/osrcheckforspecialorder/message'));
				Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('checkout/cart'))->sendResponse();
				exit;
    			}
    	    	}
    		//Mage::log('Function noFreightShipping: Ending',null,'noFreightShippingLogFile.log');
    		return true; 
    	}
}
?>