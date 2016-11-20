<?php

/**
 * Created by PhpStorm.
 * User: thanhnt
 * Date: 17/11/2016
 * Time: 15:56
 */
class SM_XReport_Model_ViewHistory extends SM_XReport_Model_Api_ServiceAbstract
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
            $historyOrder = new SM_XReport_Model_Api_Data_HistoryOrder();
            $historyOrder->addData($item->getData());
            $items[] = $historyOrder;
        }

        $dataFilters = array();
        foreach ($postData = $this->getDataFilter() as $pos) {
            if ($value = $pos->search->value != "") {
//                var_dump($pos->search->value);die;
                $columnName = $pos->data;
                $data = array();
                $data[$columnName] = $pos->search->value;
                $dataFilters[] = $data;

            }
        }
        return $this->getSearchData()
            ->setItems($items)
            ->setSearchFilter(($dataFilters));
    }

    public function getDataFilter()
    {
        $postData = json_decode(file_get_contents('php://input'));
        return $postData;
    }

    public function collectionDataFilter($collection)
    {
        $exactly = '/^".*"$/';
        $first = '/^\^.*/';
        $grand = '/(^>)=?\d*/';
        $less = '/(^<)=?\d*/';
        $eq = '/(^=)\d*/';
        $postData = $this->getDataFilter();
        foreach ($postData as $col) {
            if ($col->search->value != '') {
                $columnName = $col->data;
                if ($columnName != 'method')
                    $columnName = 'main_table.' . $columnName;
                // Loai created_at boi vi da filter o ben ngoai
                if ($columnName == 'main_table.created_at') {
                    $this->selectDateTime($col->search->value, $collection);
                    continue;
                }
                if ($columnName == 'main_table.sku') {
                    $columnName = 'sku';
                    $collection->getSelect()->joinLeft(array('odi' => 'sales_flat_order_item'),
                        'odi.order_id=main_table.entity_id', array('odi.sku'));
                    $collection->getSelect()->group('entity_id');
                }

                if ($columnName == 'main_table.store_id' && $col->search->value != 0) {
                    $collection->addFieldToFilter($columnName, $col->search->value);
                    continue;
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
//                    $searchValue = '%' . $searchValue . '%';

                    $collection->addFieldToFilter($columnName, array('like' => '%' . $col->search->value . '%'));

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
        $collection->getSelect()->joinLeft(array('a' => 'sales_flat_order_payment'), "a.parent_id = main_table.entity_id", array('method' => 'a.method'));


        // Add filter by column
        $collection = $this->collectionDataFilter($collection);
        return $collection;
    }

    public function selectDateTime($dataDateTime, $collection)
    {
        $array = explode('/', $dataDateTime);
        $dateStart = $array[0];
        $dateEnd = $array[1];
        if ($dateStart == null || $dateEnd == null) {
            $dateEnd = Mage::app()->getLocale()->date();
            $dateStart = clone  $dateStart;

            $dateEnd->setHour(23)
                ->setMinute(59)
                ->setSecond(59);
            $dateStart->setHour(0)
                ->setMinute(0)
                ->setSecond(0);
        } else {
            $dateEnd = Mage::app()->getLocale()->date($dateEnd, null, null, false);
            $dateStart = Mage::app()->getLocale()->date($dateStart, null, null, false);

            $dateEnd->setHour(23)
                ->setMinute(59)
                ->setSecond(59);

            $dateStart->setHour(0)
                ->setMinute(0)
                ->setSecond(0);
        }
        $dateRange = Mage::getModel('reports/resource_order_collection')->getDateRange($range = 'custom', $dateStart, $dateEnd);
//        var_dump($dateRange);die;
        return $collection->addFieldToFilter('main_table.created_at', $dateRange);
    }

}
