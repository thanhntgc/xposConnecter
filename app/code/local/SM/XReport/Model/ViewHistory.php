<?php

/**
 * Created by PhpStorm.
 * User: thanhnt
 * Date: 17/11/2016
 * Time: 15:56
 */
class SM_XReport_Model_ViewHistory extends SM_XRetail_Model_Api_ServiceAbstract
{


    /**
     * @var Mage_Sales_Model_Order
     */
    private $_orderModel;


    public function __construct()
    {
        $this->_orderModel = Mage::getModel('sales/order');
        parent::__construct();
    }

    public function getOrderHistory()
    {
        return $this->loadHistoryOrder()->getOutput();
    }


    public function loadHistoryOrder()
    {
        $collection = $this->getOrderCollection();
        $items = [];
        foreach ($collection as $item) {
            $historyOrder = new SM_Core_Api_Data_XReport();
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

    public function getOrderCollection()
    {
        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = $this->_orderModel->getCollection();
        $collection->addAttributeToSelect('*');
        $collection->getSelect()->joinLeft(array('a' => 'sales_flat_order_payment'), 'a.parent_id = main_table.entity_id', array('method' => 'a.method'));


        $collection->getSelect()->joinLeft(array('odi' => 'sales_flat_order_item'),
            'odi.order_id=main_table.entity_id', array('odi.sku'));
        $collection->getSelect()->group('entity_id');
        // Add filter by column
//        $collection = $this->getDataFilter($collection);
        return $collection;
    }
}