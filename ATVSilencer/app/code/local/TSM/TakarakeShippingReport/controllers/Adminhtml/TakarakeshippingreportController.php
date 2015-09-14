<?php
class TSM_TakarakeShippingReport_Adminhtml_TakarakeshippingreportController
      extends Mage_Adminhtml_Controller_Action {
 
    protected function _initAction() {
    		//Mage::log('20',null,'TakarakeShippingReportLogFile.log');
        $this->loadLayout();
    		//Mage::log('23',null,'TakarakeShippingReportLogFile.log');
        return $this;
    }
 
    public function indexAction() {
    		//Mage::log('21',null,'TakarakeShippingReportLogFile.log');
        $this->_initAction()
        ->renderLayout();
    		//Mage::log('22',null,'TakarakeShippingReportLogFile.log');
    }
 
    public function exportCsvAction() {
        // Specify filename for exported CSV file
        $fileName = 'tucker_rocky_shipping_report.csv';
        $content = $this->getLayout()->createBlock('takarakeshippingreport/adminhtml_takarakeshippingreport_grid')
           ->getCsv();
        $this->_sendUploadResponse($fileName, $content);
    }
 
    public function exportXmlAction() {
        // Specify filename for exported XML file
        $fileName = 'takarake_shipping_report.xml';
        $content = $this->getLayout()->createBlock('takarakeshippingreport/adminhtml_takarakeshippingreport_grid')
           ->getXml();
        $this->_sendUploadResponse($fileName, $content);
    }
 
    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream') {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
 
}
?>