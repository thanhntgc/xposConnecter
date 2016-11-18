<?php

class SM_Store_Model_StoreManagement extends SM_XRetail_Model_Api_ServiceAbstract
{
    /**
     * @var Mage_Core_Model_Resource_Store_Collection
     */
    protected $_storeModel;

    /**
     * @var Mage_Core_Model_Store
     */
    protected $_storeModelData;

    /**
     * @var SM_Store_Model_Locale
     */
    protected $_localFormat;

    public function __construct()
    {
        $this->_storeModel = Mage::getModel('core/resource_store_collection');
        $this->_storeModelData = Mage::getModel('core/store');
        $this->_localFormat = Mage::getModel('xstore/locale');
        parent::__construct();
    }

    public function getStoreData()
    {
        return $this->loadStore($this->getSearchCriteria())->getOutput();
    }

    public function loadStore($searchCriteria = null)
    {
//var_dump(Mage::app()->getStore()->getCurrentCurrency());
        if (is_null($searchCriteria) || !$searchCriteria) {
            $searchCriteria = $this->getSearchCriteria();
        }
        $this->getSearchResult()->setSearchCriteria($searchCriteria);
        $collection = $this->getStoreCollection($searchCriteria);
        $items = array();
        if ($collection->getLastPageNumber() < $searchCriteria->getData('currentPage')) {
        } else {
            foreach ($collection as $store) {
                $xStore = new SM_Core_Api_Data_XStore();

                $xStore->addData($store->getData());
                $baseCurrency = $store->getBaseCurrency();
                $xStore->setData('base_currency', $baseCurrency->getData());

                $currentCurrency = $this->getCurrentCurrencyBaseOnStore($store);
                $xStore->setData('current_currency', ["currency_code" => $currentCurrency]);

                $rate = $baseCurrency->getRate($currentCurrency);
                $xStore->setData('rate', $rate);

                $xStore->setData('price_format', $this->_localFormat->getPriceFormat($currentCurrency));

                $items[] = $xStore;
            }
        }
        return $this->getSearchResult()
            ->setItems($items)
            ->setTotalCount($collection->getSize());

    }

    /**
     * @param Varien_Object $searchCriteria
     * @return Mage_Core_Model_Resource_Store_Collection
     */
    protected function getStoreCollection(Varien_Object $searchCriteria)
    {
        /** @var Mage_Core_Model_Store $collection */
        $collection = $this->_storeModelData->getCollection();
        $collection->setCurPage(is_nan($searchCriteria->getData('currentPage')) ? 1 : $searchCriteria->getData('currentPage'));
        $collection->setPageSize(
            is_nan($searchCriteria->getData('pageSize')) ? SM_XRetail_Helper_DataConfig::PAGE_SIZE_LOAD_DATA : $searchCriteria->getData('pageSize')
        );
        return $collection;
    }

    /**
     * @param Mage_Core_Model_Store $store
     * @return mixed|string
     */
    protected function getCurrentCurrencyBaseOnStore(Mage_Core_Model_Store $store)
    {
        // try to get currently set code among allowed
        $code = $store->getDefaultCurrencyCode();
        if (in_array($code, $store->getAvailableCurrencyCodes(true))) {
            return $code;
        }

        // take first one of allowed codes
        $codes = array_values($store->getAvailableCurrencyCodes(true));
        if (empty($codes)) {
            // return default code, if no codes specified at all
            return $store->getDefaultCurrencyCode();
        }

        return array_shift($codes);
    }

}