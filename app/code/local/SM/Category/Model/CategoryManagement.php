<?php

/**
 * Created by PhpStorm.
 * User: thanhnt
 * Date: 16/11/2016
 * Time: 16:13
 */
class SM_Category_Model_CategoryManagement extends SM_XRetail_Model_Api_ServiceAbstract
{
    /**
     * @var Mage_Catalog_Model_Category
     */
    protected $categoryCollectionFactory;


    public function __construct()
    {
        $this->categoryCollectionFactory = Mage::getModel('catalog/category');
        parent::__construct();
    }

    public function getCategoryData()
    {
        return $this->loadXCategory($this->getSearchCriteria())->getOutput();
    }

    public function loadXCategory($searchCriteria = null)
    {
        if (is_null($searchCriteria) || !$searchCriteria)
            $searchCriteria = $this->getSearchCriteria();

        $this->getSearchResult()->setSearchCriteria($searchCriteria);
        $collection = $this->getCategoryCollection($searchCriteria);
        $items = [];
        foreach ($collection as $category) {
//            echo "<pre>";
//            var_dump($category->getData());die;
            $dataCate = new SM_Core_Api_Data_XCategory();
            $dataCate->addData($category->getData());
//            $category->load($category->getEntityId());

            $productIds = Mage::getModel('catalog/category')->load($category->getData('entity_id'))
                ->getProductCollection()
                ->setStoreId($this->getSearchCriteria()->getData('storeId'))
                ->addAttributeToSelect('*')
                ->getAllIdsCache();
            $dataCate->setData('product_ids', $productIds);
            $items[] = $dataCate;
        }

        return $this->getSearchResult()
            ->setItems($items)
            ->setTotalCount($collection->getSize());
    }


    public function getCategoryCollection(Varien_Object $searchCriteria)
    {
        $storeId = $this->getSearchCriteria()->getData('storeId');
        if (is_null($storeId)) {
            throw new Exception(__('Must have param storeId'));
        } else {
            $this->getStoreManager()->setCurrentStore($storeId);
        }

        $collection = $this->categoryCollectionFactory->getCollection();
        $collection->setStoreId($storeId);
        $collection->addIsActiveFilter();
        $collection->setCurPage(is_nan($searchCriteria->getData('currentPage')) ? 1 : $searchCriteria->getData('currentPage'));
        $collection->setPageSize(
            is_nan($searchCriteria->getData('pageSize')) ? SM_XRetail_Helper_DataConfig::PAGE_SITE_LOAD_DATA : $searchCriteria->getData('pageSize')
        );
        return $collection;
    }
}