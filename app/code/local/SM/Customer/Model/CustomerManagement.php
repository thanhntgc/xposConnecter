<?php

class SM_Customer_Model_CustomerManagement extends SM_XRetail_Model_Api_ServiceAbstract
{
    const CUSTOMER_ADDRESS_FIELDS = "entity_id,parent_id,is_active,city,country_id,firstname,lastname,middlename,postcode,region,region_id,street,telephone";
    /**
     * @var Mage_Customer_Model_Customer
     */
    private $_customerModel;
    /**
     * @var Mage_Customer_Model_Config_Share
     */
    private $customerConfigShare;

    /**
     * SM_Customer_Model_CustomerManagement constructor.
     */
    public function __construct()
    {
        $this->_customerModel = Mage::getModel('customer/customer');
        $this->customerConfigShare = Mage::getModel('customer/config_share');
        parent::__construct();
    }

    /**
     * @return array
     */
    public function getCustomerData()
    {
        return $this->loadCustomers($this->getSearchCriteria())->getOutput();
    }

    /**
     * @param null $searchCriteria
     *
     * @return SM_Core_Api_SearchResult
     */
    public function loadCustomers($searchCriteria = null)
    {
        if (is_null($searchCriteria) || !$searchCriteria) {
            $searchCriteria = $this->getSearchCriteria();
        }

        $this->getSearchResult()->setSearchCriteria($searchCriteria);
        $collection = $this->getCustomerCollection($searchCriteria);
        $customers = [];

        if ($collection->getLastPageNumber() < $searchCriteria->getData('currentPage')) {
        } else {
            foreach ($collection as $customerModel) {
                /** @var $customerModel Mage_Customer_Model_Customer */
                $customer = new SM_Core_Api_Data_XCustomer();
                $customer->addData($customerModel->getData());
                $customer->setData('tax_class_id', $customerModel->getTaxClassId());

                $customer->setData('address', $this->getCustomerAddress($customerModel));
                $customers[] = $customer;
            }

        }

        return $this->getSearchResult()
            ->setItems($customers)
            ->setTotalCount($collection->getSize());
    }

    /**
     * @param Varien_Object $searchCriteria
     * @return Mage_Customer_Model_Resource_Customer_Collection
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    protected function getCustomerCollection(Varien_Object $searchCriteria)
    {

        $storeId = $this->getSearchCriteria()->getData('storeId');
        if (is_null($storeId)) {
            throw new Exception(__('Must have param storeId'));
        } else {
            $this->getStoreManager()->setCurrentStore($storeId);
        }
        /** @var Mage_Customer_Model_Resource_Customer_Collection $collection */
        $collection = $this->_customerModel->getCollection();
        $collection->addAttributeToSelect('*');
        $collection->addNameToSelect();
        $collection->setCurPage(is_nan($searchCriteria->getData('currentPage')) ? 1 : $searchCriteria->getData('currentPage'));
        $collection->setPageSize(
            is_nan($searchCriteria->getData('pageSize')) ? SM_XRetail_Helper_DataConfig::PAGE_SIZE_LOAD_CUSTOMER : $searchCriteria->getData('pageSize')
        );
        if ($this->customerConfigShare->isWebsiteScope()) {
            $collection->addFieldToFilter('website_id', $this->getStoreManager()->getWebsite()->getId());
        }
        return $collection;
    }

    /**
     * @param Mage_Customer_Model_Customer $customer
     * @return array
     */
    protected function getCustomerAddress(Mage_Customer_Model_Customer $customer)
    {
        $customerAdd = [];

        foreach ($customer->getAddresses() as $address) {
            $addData = $address->getData();
            $_customerAdd = array_filter(
                $addData,
                function ($k) {
                    return (in_array($k, explode(",", SM_Customer_Model_CustomerManagement::CUSTOMER_ADDRESS_FIELDS)));
                },
                ARRAY_FILTER_USE_KEY);

            $_customerAdd = array_combine(
                array_map(
                    function ($key) {
                        switch ($key) {
                            case "entity_id":
                                return "id";
                            case "firstname":
                                return "first_name";
                            case "middlename":
                                return "middle_name";
                            case "lastname":
                                return "last_name";

                            default:
                                return $key;
                        }
                    },
                    array_keys($_customerAdd)),
                array_values($_customerAdd));
            $customerAdd[] = $_customerAdd;
        }

        return $customerAdd;
    }
}