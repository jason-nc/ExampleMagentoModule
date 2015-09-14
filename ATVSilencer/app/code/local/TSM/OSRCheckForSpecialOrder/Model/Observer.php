<?php
class TSM_OSRCheckForSpecialOrder_Model_Observer{
	public function __construct(){
	}
	
	public function checkIfSpecialOrder(Varien_Event_Observer $observer){
    		//Mage::log('Function checkIfSpecialOrder: Beginning.',null,'OSRCheckForSpecialOrderLogFile.log');
    		//Mage::log('	getting Quote.',null,'OSRCheckForSpecialOrderLogFile.log');
    		$cart = Mage::helper('checkout/cart')->getCart();
    		$allItems = $cart->getQuote()->getAllItems();
    		$a = end($allItems);
    		$i = count($allItems) - 1;
    		$item = $allItems[$i];   		
		$productId = Mage::getModel('catalog/product')->getIdBySku($item->getSku());
		$isSpecialOrder = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'atv_is_special_order', Mage::app()->getStore());
		$name = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'name', Mage::app()->getStore());
		$quoteId = $item->getQuoteId();
    		//Mage::log('	Name: ' . $name . ' - quote: ' . $quoteId,null,'OSRCheckForSpecialOrderLogFile.log');
		if($isSpecialOrder){
    			Mage::log('Product requires special shipping. Product Id: ' . $productId,null,'OSRCheckForSpecialOrderLogFile.log');
			Mage::getSingleton('checkout/session')->addNotice('This product is out of stock and will have a shipping delay of up one or two days in addition to the regular shipping time.');
			Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('checkout/cart'))->sendResponse();
			exit;
    		}
    	    	
    		//Mage::log('Function checkIfSpecialOrder: Ending',null,'OSRCheckForSpecialOrderLogFile.log');
    		return true; 
	}
}
?>