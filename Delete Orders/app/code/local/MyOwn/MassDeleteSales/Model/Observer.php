<?php
class MyOwn_MassDeleteSales_Model_Observer{
	public function massDelete(Varien_Event_Observer $observer){
		Mage::log('Start massDelete',null,'massDeleteLogFile.log');		
		$block = $observer->getEvent()->getBlock();
		if ($block instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction) {
			Mage::log('instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction',null,'massDeleteLogFile.log');
			if ($block->getParentBlock() instanceof Mage_Adminhtml_Block_Sales_Order_Grid){
				Mage::log('instanceof Mage_Adminhtml_Block_Sales_Order_Grid',null,'massDeleteLogFile.log');
				$block->addItem('delete_order', array(
                        		'label' => Mage::helper('sales')->__('Delete'),
                        		'url' => $block->getUrl('MassDeleteSales/adminhtml_delete/massDeleteOrders'),
                    			)
                		);
			}else if ($block->getParentBlock() instanceof Mage_Adminhtml_Block_Sales_Creditmemo_Grid){
				Mage::log('instanceof Mage_Adminhtml_Block_Sales_Creditmemo_Grid',null,'massDeleteLogFile.log');
				$block->addItem('delete_order', array(
                        		'label' => Mage::helper('sales')->__('Delete'),
                        		'url' => $block->getUrl('MassDeleteSales/adminhtml_delete/massDeleteCreditMemos'),
                    			)
                		);
			}else if ($block->getParentBlock() instanceof Mage_Adminhtml_Block_Sales_Invoice_Grid){
				Mage::log('instanceof Mage_Adminhtml_Block_Sales_Invoice_Grid',null,'massDeleteLogFile.log');
				$block->addItem('delete_order', array(
                        		'label' => Mage::helper('sales')->__('Delete'),
                        		'url' => $block->getUrl('MassDeleteSales/adminhtml_delete/massDeleteInvoices'),
                    			)
                		);
			}
		}
		Mage::log('End massDelete',null,'massDeleteLogFile.log');		
	}
}
?>