<?php
class TSM_TakarakeShippingReport_Block_Adminhtml_Takarakeshippingreport extends Mage_Adminhtml_Block_Widget_Grid_Container {
  
    public function __construct() {
    		//Mage::log('8',null,'TakarakeShippingReportLogFile.log');
        $this->_controller = 'adminhtml_takarakeshippingreport';
    		//Mage::log('9',null,'TakarakeShippingReportLogFile.log');
        $this->_blockGroup = 'takarakeshippingreport';
    		//Mage::log('10',null,'TakarakeShippingReportLogFile.log');
        $this->_headerText = Mage::helper('takarakeshippingreport')->__('Tucker Rocky Shipping Report');
    		//Mage::log('11',null,'TakarakeShippingReportLogFile.log');
        parent::__construct();
    		//Mage::log('12',null,'TakarakeShippingReportLogFile.log');
        $this->_removeButton('add');
    		//Mage::log('13',null,'TakarakeShippingReportLogFile.log');
    }
  
}
?>