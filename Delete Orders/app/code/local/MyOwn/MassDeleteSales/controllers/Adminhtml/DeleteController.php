<?php
class MyOwn_MassDeleteSales_Adminhtml_DeleteController extends Mage_Adminhtml_Controller_Action{

	private $sales_flat_creditmemo;
	private $sales_flat_creditmemo_comment;
	private $sales_flat_creditmemo_grid;
	private $sales_flat_creditmemo_item;
	private $sales_flat_invoice;
	private $sales_flat_invoice_comment;
	private $sales_flat_invoice_grid;
	private $sales_flat_invoice_item;
	private $sales_flat_order;
	private $sales_flat_order_address;
	private $sales_flat_order_grid;
	private $sales_flat_order_item;
	private $sales_flat_order_payment;
	private $sales_flat_order_status_history;
	private $sales_flat_quote;
	private $sales_flat_quote_address;
	private $sales_flat_quote_item;
	private $sales_flat_quote_payment;
	private $sales_flat_shipment;
	private $sales_flat_shipment_comment;
	private $sales_flat_shipment_grid;
	private $sales_flat_shipment_item;
	private $sales_flat_shipment_track;
	private $sales_order_tax;
	private $resource;
	private $readConnection;
	private $writeConnection;
	
	public function massDeleteOrdersAction(){
		Mage::log('Start massDeleteOrdersAction',null,'DeleteControllerLogFile.log');
		$this->getTableNames();
		$this->resource = Mage::getSingleton('core/resource');    
		$this->readConnection = $this->resource->getConnection('core_read');
		$this->writeConnection = $this->resource->getConnection('core_write');
		$orderIds = $this->getRequest()->getPost('order_ids', array());
		foreach ($orderIds as $orderId) {
			$this->deleteOrder($orderId);
		}
		$this->_redirect('adminhtml/sales_order/');
		Mage::log('End massDeleteOrdersAction',null,'DeleteControllerLogFile.log');
	}
	
	public function massDeleteCreditMemosAction(){
		Mage::log('Start massDeleteCreditMemosAction',null,'DeleteControllerLogFile.log');
		$this->getTableNames();
		$this->resource = Mage::getSingleton('core/resource');    
		$this->readConnection = $this->resource->getConnection('core_read');
		$this->writeConnection = $this->resource->getConnection('core_write');
		$creditMemoIds = $this->getRequest()->getPost('creditmemo_ids', array());
		foreach ($creditMemoIds as $creditMemoId) {
			$this->deleteCreditMemo($creditMemoId);
		}
		$this->_redirect('adminhtml/sales_creditmemo/');
		Mage::log('End massDeleteCreditMemosAction',null,'DeleteControllerLogFile.log');
	}
	
	public function massDeleteInvoicesAction(){
		Mage::log('Start massDeleteInvoicesAction',null,'DeleteControllerLogFile.log');
		$this->getTableNames();
		$this->resource = Mage::getSingleton('core/resource');    
		$this->readConnection = $this->resource->getConnection('core_read');
		$this->writeConnection = $this->resource->getConnection('core_write');
		$invoiceIds = $this->getRequest()->getPost('invoice_ids', array());
		foreach ($invoiceIds as $invoiceId) {
			$this->deleteInvoice($invoiceId);
		}
		$this->_redirect('adminhtml/sales_invoice/');
		Mage::log('End massDeleteInvoicesAction',null,'DeleteControllerLogFile.log');
	}
	
	private function deleteInvoice($invoiceId){
		$this->_getSession()->addError($this->__('Invoice Id: ' . $creditMemoId));
		$query = 'SELECT order_id, grand_total FROM ' . $this->sales_flat_invoice . ' WHERE entity_id = ' . $invoiceId;	
		$this->_getSession()->addError($this->__('query: ' . $query));
		$invoiceTable = $this->readConnection->fetchAll($query);
			
		$query = 'DELETE FROM ' . $this->sales_flat_invoice . ' WHERE entity_id = ' . $invoiceId;
		$this->_getSession()->addError($this->__('query: ' . $query));
		$this->writeConnection->query($query);
		$query = 'DELETE FROM ' . $this->sales_flat_invoice_grid . ' WHERE entity_id = ' . $invoiceId;
		$this->_getSession()->addError($this->__('query: ' . $query));
		$this->writeConnection->query($query);
			
		$query = 'SELECT qty, order_item_id FROM ' . $this->sales_flat_invoice_item . ' WHERE parent_id = ' . $invoiceId;
		$this->_getSession()->addError($this->__('query: ' . $query));
		$invoiceItemTable = $this->readConnection->fetchAll($query);
		
		$query = 'SELECT total_invoiced  FROM ' . $this->sales_flat_order . ' WHERE entity_id = ' . $invoiceTable[0]['order_id'];
		$this->_getSession()->addError($this->__('query: ' . $query));
		$baseTotalRefundedFromOrder = $this->readConnection->fetchAll($query);
		$dif = $baseTotalRefundedFromOrder[0]['total_invoiced'] - $invoiceTable[0]['grand_total'];
		
		$query = 'UPDATE ' . $this->sales_flat_order . ' SET base_total_invoiced = ' . $dif . ', total_invoiced = ' . $dif . ', base_shipping_invoiced = 0, shipping_invoiced = 0, base_shipping_invoiced = 0, status = "pending", state = "new" WHERE entity_id = ' . $invoiceTable[0]['order_id'];
		$this->_getSession()->addError($this->__('query: ' . $query));
		$this->writeConnection->query($query);
	
		$stop = count($invoiceItemTable);
		for($index = 0; $index < $stop; $index++){
			$query = 'SELECT qty_invoiced FROM ' . $this->sales_flat_order_item . ' WHERE item_id = ' . $invoiceItemTable[0]['order_item_id'];
			$this->_getSession()->addError($this->__('query: ' . $query));
			$qtyRefunded = $this->readConnection->fetchAll($query);	
			
			$dif = $qtyRefunded[0]['qty_invoiced'] - $invoiceItemTable[$index]['qty'];
			$query = 'UPDATE ' . $this->sales_flat_order_item . ' SET qty_invoiced = ' . $dif . ' WHERE item_id = ' . $invoiceItemTable[0]['order_item_id'];
			$this->_getSession()->addError($this->__('query: ' . $query));
			$this->writeConnection->query($query);
		}	
	}
	
	private function deleteCreditMemo($creditMemoId){
		$this->_getSession()->addError($this->__('Credit Memo Id: ' . $creditMemoId));
		$query = 'SELECT order_id, grand_total FROM ' . $this->sales_flat_creditmemo . ' WHERE entity_id = ' . $creditMemoId;	
		$this->_getSession()->addError($this->__('query: ' . $query));
		$creditMemoTable = $this->readConnection->fetchAll($query);
			
		$query = 'DELETE FROM ' . $this->sales_flat_creditmemo . ' WHERE entity_id = ' . $creditMemoId;
		$this->writeConnection->query($query);
		$query = 'DELETE FROM ' . $this->sales_flat_creditmemo_grid . ' WHERE entity_id = ' . $creditMemoId;
		$this->writeConnection->query($query);
			
		$query = 'SELECT qty, order_item_id FROM ' . $this->sales_flat_creditmemo_item . ' WHERE parent_id = ' . $creditMemoId;
		$creditMemoItemTable = $this->readConnection->fetchAll($query);
		
		$query = 'SELECT total_refunded, total_offline_refunded, total_online_refunded  FROM ' . $this->sales_flat_order . ' WHERE entity_id = ' . $creditMemoTable[0]['order_id'];
		$x = 'total_offline_refunded';
		if($baseTotalRefundedFromOrder[0]['total_online_refunded'] > 0){
			$x = 'total_online_refunded';
			$query = 'SELECT base_total_refunded  FROM ' . $this->sales_flat_invoice . ' WHERE order_id = ' . $creditMemoTable[0]['order_id'];			
			$baseTotalRefunded = $this->readConnection->fetchAll($query);
			$dif = $baseTotalRefunded[0]['base_total_refunded'] - $creditMemoTable[0]['grand_total'];
			$query = 'UPDATE ' . $this->sales_flat_invoice . ' SET base_total_refunded = ' . $dif . ' WHERE entity_id = ' . $creditMemoTable[0]['order_id'];
			$this->writeConnection->query($query);
		}
		
		$baseTotalRefundedFromOrder = $this->readConnection->fetchAll($query);
		$dif = $baseTotalRefundedFromOrder[0]['total_refunded'] - $creditMemoTable[0]['grand_total'];
		
		$query = 'UPDATE ' . $this->sales_flat_order . ' SET base_total_refunded = ' . $dif . ', total_refunded = ' . $dif . ', ' . $x . ' = ' . $dif . ', base_shipping_refunded = 0, shipping_refunded = 0, base_shipping_tax_refunded = 0 WHERE entity_id = ' . $creditMemoTable[0]['order_id'];
		$this->writeConnection->query($query);
	
		$stop = count($creditMemoItemTable);
		for($index = 0; $index < $stop; $index++){
			$query = 'SELECT qty_refunded FROM ' . $this->sales_flat_order_item . ' WHERE item_id = ' . $creditMemoItemTable[0]['order_item_id'];
		$this->_getSession()->addError($this->__('query: ' . $query));
			$qtyRefunded = $this->readConnection->fetchAll($query);	
			
			$dif = $qtyRefunded[0]['qty_refunded'] - $creditMemoItemTable[$index]['qty'];
			$query = 'UPDATE ' . $this->sales_flat_order_item . ' SET qty_refunded = ' . $dif . ' WHERE item_id = ' . $creditMemoItemTable[0]['order_item_id'];
			$this->writeConnection->query($query);
		}
	
	}
	
	private function deleteOrder($orderId){
		$this->_getSession()->addError($this->__('Delete this order: ' . $orderId));
		$query = 'SELECT entity_id FROM ' . $this->sales_flat_creditmemo . ' WHERE order_id = ' . $orderId;
		$result = $this->readConnection->fetchAll($query);
		$stop = count($result);
		
		$query = 'DELETE FROM ' . $this->sales_flat_creditmemo . ' WHERE order_id = ' . $orderId;
		$this->writeConnection->query($query);
		for($index = 0; $index < $stop; $index++){
			$query = 'DELETE FROM ' . $this->sales_flat_creditmemo_comment . ' WHERE parent_id = ' . $result[$index]['entity_id'];
			$this->writeConnection->query($query);			
		}
		$query = 'DELETE FROM ' . $this->sales_flat_creditmemo_grid . ' WHERE order_id = ' . $orderId;
		$this->writeConnection->query($query);
		for($index = 0; $index < $stop; $index++){
			$query = 'DELETE FROM ' . $this->sales_flat_creditmemo_item . ' WHERE parent_id = ' . $result[$index]['entity_id'];
			$this->writeConnection->query($query);
		}
		$query = 'SELECT entity_id FROM ' . $this->sales_flat_invoice . ' WHERE order_id = ' . $orderId;
		$result = $this->readConnection->fetchAll($query);
		$stop = count($result);
		
		$query = 'DELETE FROM ' . $this->sales_flat_invoice . ' WHERE order_id = ' . $orderId;
		$this->writeConnection->query($query);
		for($index = 0; $index < $stop; $index++){
			$query = 'DELETE FROM ' . $this->sales_flat_invoice_comment . ' WHERE parent_id = ' . $result[$index]['entity_id'];
			$this->writeConnection->query($query);
		}
		$query = 'DELETE FROM ' . $this->sales_flat_invoice_grid . ' WHERE order_id = ' . $orderId;
		$this->writeConnection->query($query);
		for($index = 0; $index < $stop; $index++){
			$query = 'DELETE FROM ' . $this->sales_flat_invoice_item . ' WHERE parent_id = ' . $result[$index]['entity_id'];
			$this->writeConnection->query($query);
		}
		$query = 'DELETE FROM ' . $this->sales_flat_order . ' WHERE entity_id = ' . $orderId;
		$this->writeConnection->query($query);
		$query = 'DELETE FROM ' . $this->sales_flat_order_address . ' WHERE entity_id = ' . $orderId;
		$this->writeConnection->query($query);
		$query = 'DELETE FROM ' . $this->sales_flat_order_grid . ' WHERE entity_id = ' . $orderId;
		$this->writeConnection->query($query);
		$query = 'DELETE FROM ' . $this->sales_flat_order_item . ' WHERE order_id = ' . $orderId;
		$this->writeConnection->query($query);
		$query = 'DELETE FROM ' . $this->sales_flat_order_payment . ' WHERE entity_id = ' . $orderId;
		$this->writeConnection->query($query);
		$query = 'DELETE FROM ' . $this->sales_flat_order_status_history . ' WHERE entity_id = ' . $orderId;
		$this->writeConnection->query($query);
		$query = 'DELETE FROM ' . $this->sales_flat_quote . ' WHERE entity_id = ' . $orderId;
		$this->writeConnection->query($query);
		$query = 'DELETE FROM ' . $this->sales_flat_quote_address . ' WHERE quote_id = ' . $orderId;
		$this->writeConnection->query($query);
		$query = 'DELETE FROM ' . $this->sales_flat_quote_item . ' WHERE quote_id = ' . $orderId;
		$this->writeConnection->query($query);
		$query = 'DELETE FROM ' . $this->sales_flat_quote_payment . ' WHERE quote_id = ' . $orderId;
		$this->writeConnection->query($query);
		$query = 'DELETE FROM ' . $this->sales_flat_shipment . ' WHERE order_id = ' . $orderId;
		$this->writeConnection->query($query);
		$query = 'DELETE FROM ' . $this->sales_flat_shipment_comment . ' WHERE entity_id = ' . $orderId;
		$this->writeConnection->query($query);
		$query = 'DELETE FROM ' . $this->sales_flat_shipment_grid . ' WHERE order_id = ' . $orderId;
		$this->writeConnection->query($query);
		$query = 'DELETE FROM ' . $this->sales_flat_shipment_item . ' WHERE entity_id = ' . $orderId;
		$this->writeConnection->query($query);
		$query = 'DELETE FROM ' . $this->sales_flat_shipment_track . ' WHERE order_id = ' . $orderId;
		$this->writeConnection->query($query);
		$query = 'DELETE FROM ' . $this->sales_order_tax . ' WHERE order_id = ' . $orderId;
		$this->writeConnection->query($query);
	}
	
	private function getTableNames(){	
		$this->sales_flat_creditmemo = Mage::getSingleton("core/resource")->getTableName('sales_flat_creditmemo');
		$this->sales_flat_creditmemo_comment = Mage::getSingleton("core/resource")->getTableName('sales_flat_creditmemo_comment');
		$this->sales_flat_creditmemo_grid = Mage::getSingleton("core/resource")->getTableName('sales_flat_creditmemo_grid');
		$this->sales_flat_creditmemo_item = Mage::getSingleton("core/resource")->getTableName('sales_flat_creditmemo_item');
		$this->sales_flat_invoice = Mage::getSingleton("core/resource")->getTableName('sales_flat_invoice');
		$this->sales_flat_invoice_comment = Mage::getSingleton("core/resource")->getTableName('sales_flat_invoice_comment');
		$this->sales_flat_invoice_grid = Mage::getSingleton("core/resource")->getTableName('sales_flat_invoice_grid');
		$this->sales_flat_invoice_item = Mage::getSingleton("core/resource")->getTableName('sales_flat_invoice_item');
		$this->sales_flat_order = Mage::getSingleton("core/resource")->getTableName('sales_flat_order');
		$this->sales_flat_order_address = Mage::getSingleton("core/resource")->getTableName('sales_flat_order_address');
		$this->sales_flat_order_grid = Mage::getSingleton("core/resource")->getTableName('sales_flat_order_grid');
		$this->sales_flat_order_item = Mage::getSingleton("core/resource")->getTableName('sales_flat_order_item');
		$this->sales_flat_order_payment = Mage::getSingleton("core/resource")->getTableName('sales_flat_order_payment');
		$this->sales_flat_order_status_history = Mage::getSingleton("core/resource")->getTableName('sales_flat_order_status_history');
		$this->sales_flat_quote = Mage::getSingleton("core/resource")->getTableName('sales_flat_quote');
		$this->sales_flat_quote_address = Mage::getSingleton("core/resource")->getTableName('sales_flat_quote_address');
		$this->sales_flat_quote_item = Mage::getSingleton("core/resource")->getTableName('sales_flat_quote_item');
		$this->sales_flat_quote_payment = Mage::getSingleton("core/resource")->getTableName('sales_flat_quote_payment');
		$this->sales_flat_shipment = Mage::getSingleton("core/resource")->getTableName('sales_flat_shipment');
		$this->sales_flat_shipment_comment = Mage::getSingleton("core/resource")->getTableName('sales_flat_shipment_comment');
		$this->sales_flat_shipment_grid = Mage::getSingleton("core/resource")->getTableName('sales_flat_shipment_grid');
		$this->sales_flat_shipment_item = Mage::getSingleton("core/resource")->getTableName('sales_flat_shipment_item');
		$this->sales_flat_shipment_track = Mage::getSingleton("core/resource")->getTableName('sales_flat_shipment_track');
		$this->sales_order_tax = Mage::getSingleton("core/resource")->getTableName('sales_order_tax');	
	}
}
?>