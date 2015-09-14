<?php
class TSM_TakarakeShippingReport_Block_Takarakeshippingreport extends Mage_Core_Block_Template {
 
    public function _prepareLayout() {
    		//Mage::log('14',null,'TakarakeShippingReportLogFile.log');
        return parent::_prepareLayout();
    }
 
    public function getReportNewOrders() {
    		//Mage::log('15',null,'TakarakeShippingReportLogFile.log');
        if (!$this->hasData('takarakeshippingreport')) {
            $this->setData('takarakeshippingreport', Mage::registry('takarakeshippingreport'));
        }
    		//Mage::log('16',null,'TakarakeShippingReportLogFile.log');
        return $this->getData('takarakeshippingreport');
    }
 
}
?>