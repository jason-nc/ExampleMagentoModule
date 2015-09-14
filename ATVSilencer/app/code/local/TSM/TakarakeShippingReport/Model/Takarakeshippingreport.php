<?php
class TSM_Takarakeshippingreport_Model_Takarakeshippingreport extends Mage_Reports_Model_Mysql4_Product_Ordered_Collection
{
    function __construct() {
    		//Mage::log('Function __construct(): Beginning.',null,'ModelReportnewordersLogFile.log');
        parent::__construct();
    }
 
    /**
     * Join fields
     *
     * @param int $from
     * @param int $to
     * @return Mage_Reports_Model_Resource_Product_Ordered_Collection
     */
    protected function _joinFields($from = '', $to = '')
    {
    		//Mage::log('Function _joinFields: Beginning.',null,'ModelReportnewordersLogFile.log');
        $this->addAttributeToSelect('name')
            ->addAttributeToSelect('increment_id')
            ->addOrderedQty($from, $to)
            ->setOrder('sku', self::SORT_ORDER_ASC);
         
        //Mage::log('SQL: '.$this->getSelect()->__toString());
        return $this;
    }
 
    /**
     * Add ordered qty's
     *
     * @param string $from
     * @param string $to
     * @return Mage_Reports_Model_Resource_Product_Collection
     */
    public function addOrderedQty($from = '', $to = '')
    {
    		//Mage::log('Function addOrderedQty: Beginning.',null,'ModelReportnewordersLogFile.log');
        $adapter              = $this->getConnection();
        $compositeTypeIds     = Mage::getSingleton('catalog/product_type')->getCompositeTypes();
        $orderTableAliasName  = $adapter->quoteIdentifier('order');
        $addressTableAliasName  = 'a';
     
        $orderJoinCondition   = array(
                $orderTableAliasName . '.entity_id = order_items.order_id',
                $adapter->quoteInto("{$orderTableAliasName}.state = ?", Mage_Sales_Model_Order::STATE_PROCESSING),
     
        );
     
        $addressJoinCondition = array(
                $addressTableAliasName . '.entity_id = order.shipping_address_id'
        );
         
        $productJoinCondition = array(
                //$adapter->quoteInto('(e.type_id NOT IN (?))', $compositeTypeIds),
                'e.entity_id = order_items.product_id',
                $adapter->quoteInto('e.entity_type_id = ?', $this->getProductEntityTypeId())
        );
         
        if ($from != '' && $to != '') {
            $fieldName            = $orderTableAliasName . '.created_at';
            $orderJoinCondition[] = $this->_prepareBetweenSql($fieldName, $from, $to);
        }
     
        $this->getSelect()->reset()
        ->from(
                array('order_items' => $this->getTable('sales/order_item')),
                array(
                        'new_order' => new Zend_Db_Expr('"NEWORDER"'),
                        'freight_carrier' => new Zend_Db_Expr('"UG"'),
                        'address2' => new Zend_Db_Expr('""'),
                        'qty_ordered' => 'order_items.qty_ordered',
                        'order_items_name' => 'order_items.name',
                        'order_increment_id' => 'order.increment_id',
                        'product_id' => 'order_items.product_id'
                ))
                ->joinInner(
                        array('order' => $this->getTable('sales/order')),
                        implode(' AND ', $orderJoinCondition),
                        array())
                        ->joinLeft(
                                array('a' => $this->getTable('sales/order_address')),
                                implode(' AND ', $addressJoinCondition),
                                array(
                                        'shipto_street' => 'a.street',
                                        'shipto_city' => 'a.city',
                                        'shipto_state' => 'a.region',
                                        'shipto_country_id' => 'a.country_id',
                                        'shipto_postcode' => 'a.postcode',
                                        'shipto_name' => "CONCAT(COALESCE(a.firstname, ''), ' ', COALESCE(a.lastname, ''))"
                                ),
                        array())
                         
                        ->joinLeft(
                                array('e' => $this->getProductEntityTableName()),
                                implode(' AND ', $productJoinCondition),
                                array(
                                        'created_at' => 'e.created_at',
                                        'updated_at' => 'e.updated_at'
                                ))
                         
                        ->joinLeft(
                                array('t' => 'mgtcatalog_product_entity_varchar'),
                                't.entity_id = order_items.product_id AND t.attribute_id = 140',
                                array(
                                        'trNumber' => 't.value'
                                ))                                
                        ->joinLeft(
                                array('n' => 'mgtdirectory_country_region'),
                                'n.default_name = a.region',
                                array(
                                        'state_initial' => 'n.code'
                                ))
                        ->where('parent_item_id IS NULL')
                        //->group('order_items.product_id')
                        ->having('order_items.qty_ordered > ?', 0)
                        ->order(array('order.increment_id'));
        return $this;
    }
     
    /**
     * Adding item to item array
     *
     * @param   Varien_Object $item
     * @return  Varien_Data_Collection
     */
    public function addItem(Varien_Object $item)
    {
    		//Mage::log('Function addItem: Beginning.',null,'ModelReportnewordersLogFile.log');
        $itemId = $this->_getItemId($item);
     
        if (!is_null($itemId)) {
            if (isset($this->_items[$itemId])) {
                // Unnecessary exception - http://www.magentocommerce.com/boards/viewthread/10634/P0/
                //throw new Exception('Item ('.get_class($item).') with the same id "'.$item->getId().'" already exist');
            }
            $this->_items[$itemId] = $item;
        } else {
            $this->_items[] = $item;
        }
        return $this;
    }
}
?>