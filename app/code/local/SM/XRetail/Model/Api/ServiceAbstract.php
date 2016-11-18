<?php


abstract class SM_XRetail_Model_Api_ServiceAbstract extends Mage_Core_Model_Abstract
{
    /**
     * @var Mage_Core_Controller_Request_Http
     */
    public $_request;

    /**
     * @var SM_Core_Api_SearchResult
     */
    public $_searchResult;

    /**
     * @var SM_Core_Model_DataObject
     */
    public $_searchCriteria;
    /**
     * @var SM_XRetail_Helper_DataConfig
     */
    public $_dataConfig;

    /**
     * @var Mage_Core_Model_App
     */
    public $storeManager;


    public function __construct()
    {
        $this->_request = Mage::app()->getRequest();
        $this->_dataConfig = Mage::helper('xretail/dataConfig');
        $this->storeManager = Mage::app();
//        parent::__construct();
    }

    /**
     * @return SM_Core_Api_SearchResult
     */
    public function getSearchResult()
    {
        if (is_null($this->_searchResult)) {
            $this->_searchResult = new SM_Core_Api_SearchResult();
        }
        return $this->_searchResult;
    }

    /**
     * @param SM_Core_Api_SearchResult $searchResult
     */
    public function setSearchResult($searchResult)
    {
        $this->_searchResult = $searchResult;
    }

    /**
     * @return Mage_Core_Controller_Request_Http
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Retrieve Search Criteria As DataObject
     * @return Varien_Object
     * @throws Exception
     */
    public function getSearchCriteria()
    {
        if (is_null($this->_searchCriteria)) {
            if (is_null($this->getRequest()->getParam('searchCriteria'))) {
                throw new Exception('Not found field: searchCriteria');
            } else {
                $this->_searchCriteria = new Varien_Object($this->getRequest()->getParam('searchCriteria'));
            }
        }
        return $this->_searchCriteria;
    }

    /**
     * @return SM_XRetail_Helper_DataConfig
     */
    public function getDataConfig()
    {
        return $this->_dataConfig;
    }

    /**
     * @return Mage_Core_Model_App
     */
    public function getStoreManager()
    {
        return $this->storeManager;
    }

}