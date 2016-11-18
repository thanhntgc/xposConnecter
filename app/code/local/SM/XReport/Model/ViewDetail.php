<?php

/**
 * Created by PhpStorm.
 * User: thanhnt
 * Date: 18/11/2016
 * Time: 14:52
 */
class SM_XReport_Model_ViewDetail extends SM_XRetail_Model_Api_ServiceAbstract
{


    /**
     * @var Mage_Sales_Model_Order
     */
    private $_orderModel;

    /**
     * @var Mage_Sales_Model_Order_Item
     */
    private $_orderItemModel;

    public function __construct()
    {
        $this->_orderModel = Mage::getModel('sales/order');
        $this->_orderItemModel = Mage::getModel('sales/order_item');
        parent::__construct();
    }

    public function getOrderDetail()
    {
        return $this->loadOrderDetail($this->getSearchCriteria())->getOutput();
    }


    public function loadOrderDetail($searchCriteria = null)
    {
        $collection = $this->getOrderCollection($searchCriteria);
        $items = [];
        foreach ($collection as $item) {
            $historyOrder = new SM();
            $historyOrder->addData($item->getData());
//            var_dump($item->getData());die;
//            var_dump($historyOrder);
//            die;
            $items[] = $historyOrder;
        }

        return $this->getSearchResult()
            ->setItems($items)
            ->setTotalCount($collection->getSize());

    }

    public function getDataFilter($collection)
    {
        $exactly = '/^".*"$/';
        $first = '/^\^.*/';
        $grand = '/(^>)=?\d*/';
        $less = '/(^<)=?\d*/';
        $eq = '/(^=)\d*/';
        $postData = json_decode(file_get_contents('php://input'));

        foreach ($postData as $col) {
            if ($col->search->value != '') {
                $columnName = $col->data;
                if ($columnName != 'method')
                    $columnName = 'main_table.' . $columnName;
                // Loai created_at boi vi da filter o ben ngoai
                if ($columnName == 'main_table.created_at')
                    continue;
                if ($columnName == 'main_table.sku') {
                    $columnName = 'sku';
                    $collection->getSelect()->joinLeft(array('odi' => 'sales_flat_order_item'),
                        'odi.order_id=main_table.entity_id', array('odi.sku'));
                    $collection->getSelect()->group('entity_id');
                }
                $searchValue = $col->search->value;
                if (preg_match($first, $searchValue)) {
                    $names = str_replace('^', '', $searchValue);
                    $searchValue = $names . '%';
                    $collection->addFieldToFilter($columnName, array('like' => $searchValue));
                } else if (preg_match($exactly, $searchValue)) {
                    $searchValue = str_replace('"', '', $searchValue);
                    $collection->addFieldToFilter($columnName, array('eq' => $searchValue));
                } else if (preg_match($grand, $searchValue)) {
                    $searchValue = str_replace('>', '', $searchValue);
                    $collection->addFieldToFilter($columnName, array('gteq' => $searchValue));
                } else if (preg_match($less, $searchValue)) {
                    $searchValue = str_replace('<', '', $searchValue);
                    $collection->addFieldToFilter($columnName, array('lteq' => $searchValue));
                } else if (preg_match($eq, $searchValue)) {
                    $searchValue = str_replace('=', '', $searchValue);
                    $collection->addFieldToFilter($columnName, array('eq' => $searchValue));
                } else {
                    /*TODO: first*/
                    $searchValue = '%' . $searchValue . '%';
                    $collection->addFieldToFilter($columnName, array('like' => $searchValue));
                }
            }
        }
        return $collection;
    }

    public function getOrderCollection($searchCriteria)
    {

        $dataFillter = $this->getSearchCriteria()->getData('dataFillter');
        if ($dataFillter == 'perItem') {
            /** @var Mage_Sales_Model_Order_Item $collection */
            $collection = $this->_orderItemModel->getCollection();
            $collection->addAttributeToSelect('*');
            $collection->join(array('order' => 'sales/order'), '`order`.`entity_id`=`main_table`.`order_id`', array('total_refunded', 'total_qty_ordered', 'shipping_tax_refunded', 'shipping_tax_amount', 'base_subtotal_refunded', 'grand_total', 'shipping_amount', 'base_subtotal'), null, 'left');
            $collection->getSelect()
                ->columns('COUNT(*) AS quantity')
                ->columns('SUM(order.grand_total) as grand_total')
                ->columns('SUM(shipping_amount) as shipping_amount')
                ->columns('SUM(base_subtotal) as base_subtotal')
                ->columns('SUM(main_table.base_tax_amount) as base_tax_amount')
                ->columns('SUM(base_subtotal_refunded) as base_subtotal_refunded')
                ->columns('SUM(main_table.discount_amount) as discount_amount')
                ->columns('SUM(shipping_tax_amount) as shipping_tax_amount')
                ->columns('SUM(shipping_tax_refunded) as shipping_tax_refunded')
                ->columns('SUM(shipping_refunded) as shipping_refunded')
                ->columns('SUM(total_qty_ordered) as total_qty_ordered')
                ->columns('SUM(total_refunded) as total_refunded');
        } else {
            /* @var $collection Mage_Sales_Model_Order */
            $collection = $this->_orderModel->getCollection();
            $collection->addAttributeToSelect('*');
            $collection->getSelect()->joinLeft(array('a' => 'customer_entity'), 'a.email=main_table.customer_email', array('a.customer_group_id' => 'a.group_id'));
            $collection->getSelect()->joinLeft(array('b' => 'customer_group'), 'b.customer_group_id=a.group_id', array('customer_group_code' => 'b.customer_group_code'));
            $collection->getSelect()
                ->columns('COUNT(*) AS quantity')
                ->columns('SUM(grand_total) as grand_total')
                ->columns('SUM(main_table.shipping_amount) as shipping_amount')
                ->columns('SUM(base_subtotal) as base_subtotal')
                ->columns('SUM(base_tax_amount) as base_tax_amount')
                ->columns('SUM(base_subtotal_refunded) as base_subtotal_refunded')
                ->columns('SUM(discount_amount) as discount_amount')
                ->columns('SUM(shipping_tax_amount) as shipping_tax_amount')
                ->columns('SUM(shipping_tax_refunded) as shipping_tax_refunded')
                ->columns('SUM(main_table.shipping_refunded) as shipping_refunded')
                ->columns('SUM(total_qty_ordered) as total_qty_ordered')
                ->columns('SUM(total_refunded) as total_refunded');
        }
        die($collection->getSelect());
        return $collection;
    }
}