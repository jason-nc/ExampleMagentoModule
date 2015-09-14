<?php
class TSM_TakarakeShippingReport_Block_Adminhtml_Takarakeshippingreport_Grid extends Mage_Adminhtml_Block_Report_Grid {
  
    public function __construct() {
        parent::__construct();
        $this->setId('takarakeshippingreportGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setSubReportSize(false);
    }
  
    protected function _prepareCollection() {
        parent::_prepareCollection();
        // Get the data collection from the model
        $this->getCollection()->initReport('takarakeshippingreport/takarakeshippingreport');
        return $this;
    }
  
    protected function _prepareColumns() {
        // Add columns to the grid
         /*
        $this->addColumn('test', array(
                'header' => Mage::helper('takarakeshippingreport')->__('Test'),
                'align' => 'left',
                'sortable' => false,
                'index' => 'quote_item_id'
        ));
        */
        $this->addColumn('new_order', array(
                'header' => Mage::helper('takarakeshippingreport')->__('NEWORDER'),
                'align' => 'left',
                'sortable' => false,
                'index' => 'new_order'
        ));
        $this->addColumn('order_increment_id', array(
                'header' => Mage::helper('takarakeshippingreport')->__('P'),
                'align' => 'left',
                'sortable' => false,
                'index' => 'order_increment_id'
        ));
        $this->addColumn('shipto_name', array(
                'header' => Mage::helper('takarakeshippingreport')->__('N'),
                'align' => 'left',
                'sortable' => false,
                'index' => 'shipto_name'
        ));
        $this->addColumn('shipto_street', array(
                'header' => Mage::helper('takarakeshippingreport')->__('A1'),
                'align' => 'left',
                'sortable' => false,
                'index' => 'shipto_street'
        ));
        $this->addColumn('address2', array(
                'header' => Mage::helper('takarakeshippingreport')->__('A2'),
                'align' => 'left',
                'sortable' => false,
                'index' => 'address2'
        ));
        $this->addColumn('shipto_city', array(
                'header' => Mage::helper('takarakeshippingreport')->__('C'),
                'align' => 'left',
                'sortable' => false,
                'index' => 'shipto_city'
        ));
        $this->addColumn('state_initial', array(
                'header' => Mage::helper('takarakeshippingreport')->__('S'),
                'align' => 'left',
                'sortable' => false,
                'index' => 'state_initial'
        ));
        $this->addColumn('shipto_postcode', array(
                'header' => Mage::helper('takarakeshippingreport')->__('Z'),
                'align' => 'left',
                'sortable' => false,
                'index' => 'shipto_postcode'
        ));
        $this->addColumn('freight_carrier', array(
                'header' => Mage::helper('takarakeshippingreport')->__('F'),
                'align' => 'left',
                'sortable' => false,
                'index' => 'freight_carrier'
        ));
        $this->addColumn('trNumber', array(
                'header' => Mage::helper('takarakeshippingreport')->__('Item #'),
                'align' => 'left',
                'sortable' => false,
                'index' => 'trNumber'
        ));
        $this->addColumn('qty_ordered', array(
                'header' => Mage::helper('takarakeshippingreport')->__('Qty'),
                'align' => 'left',
                'sortable' => false,
                'index' => 'qty_ordered'
        ));
 
                  
        $this->addExportType('*/*/exportCsv', Mage::helper('takarakeshippingreport')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('takarakeshippingreport')->__('XML'));
        return parent::_prepareColumns();
    }
  
    public function getRowUrl($row) {
        return false;
    }
  
    public function getReport($from, $to) {
        if ($from == '') {
            $from = $this->getFilter('report_from');
        }
        if ($to == '') {
            $to = $this->getFilter('report_to');
        }
        
        $totalObj = Mage::getModel('reports/totals');
        $totals = $totalObj->countTotals($this, $from, $to);
        $this->setTotals($totals);
        $this->addGrandTotals($totals);
       
        return $this->getCollection()->getReport($from, $to);
    }
}
?>