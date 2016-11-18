<?php

class SM_Product_Model_ProductManagement extends SM_XRetail_Model_Api_ServiceAbstract
{
    /**
     * @var Mage_Catalog_Model_Product
     */
    protected $_productModel;

    /**
     * @var SM_Product_Helper_CustomSales_Data
     */
    protected $_customSalesHelper;

    /**
     * @var SM_Product_Model_ProductManagement_ProductOptions
     */
    private $productOptions;

    /**
     * @var Mage_Catalog_Model_Product_Media_Config
     */
    private $productMediaConfig;

    /**
     * @var
     */
    private $productAttribute;

    /**
     * @var SM_Product_Model_ProductManagement_ProductStock
     */
    private $productStock;

    /**
     * @var SM_Product_Model_ProductManagement_ProductPrice
     */
    private $productPrice;

    /**
     * @var SM_Core_Api_Data_XProduct
     */
    private $xProduct;


    public function __construct()
    {
        $this->xProduct = new SM_Core_Api_Data_XProduct();
        $this->_productModel = Mage::getModel('catalog/product');
        $this->productPrice = Mage::getModel('xproduct/productManagement_productPrice');
        $this->productAttribute = Mage::getModel('xproduct/productManagement_productAttribute');
        $this->productOptions = Mage::getModel('xproduct/productManagement_productOptions');
        $this->productMediaConfig = Mage::getModel('catalog/product_media_config');
        $this->_customSalesHelper = Mage::helper('xproduct/customSales_data');
        $this->productStock = Mage::getModel('xproduct/productManagement_productStock');
        return parent::__construct();
    }

    /**
     * TODO: API TO GET PRODUCT
     */

    /**
     * @return array
     * @throws Exception
     */
    public function getProductData()
    {
        return $this->loadXProducts($this->getSearchCriteria())->getOutput();
    }

    /**
     * @param null $searchCriteria
     * @return SM_Core_Api_SearchResult
     * @throws Exception
     */
    public function loadXProducts($searchCriteria = null)
    {
        if (is_null($searchCriteria) || !$searchCriteria) {
            $searchCriteria = $this->getSearchCriteria();
        }

        $this->getSearchResult()->setSearchCriteria($searchCriteria);
        $collection = $this->getProductCollection($searchCriteria);
        $items = [];
        if ($collection->getLastPageNumber() < $searchCriteria->getData('currentPage')) {
        } else {
            foreach ($collection as $item) {
                $product = Mage::getModel('catalog/product')->load($item->getId());
                /** @var Mage_Catalog_Model_Product $item */
                if ($item->getId() == $this->getCustomSalesHelper()->getCustomSalesId()) {
                    continue;
                }
                /** @var SM_Core_Api_Data_XProduct $xProduct */
                $xProduct = new SM_Core_Api_Data_XProduct();

                $xProduct->setData(($item));
                $xProduct->addData($item->getData());

                $xProduct->setData('tier_prices',
                    $this->getProductPrice()->getExistingPrices($item, 'tier_price', true));

                $xProduct->setData('store_id', $this->getStoreManager()->getStore()->getId());

                if (is_null($item->getImage()) || $item->getImage() == 'no_selection' || !$item->getImage()) {
                    $xProduct->setData('origin_image', null);
                } else {
                    $xProduct->setData('origin_image', $this->getProductMediaConfig()->getMediaUrl($item->getImage()));
                }
                $xProduct->setData('custom_attributes', $this->getProductAttribute()->getCustomAttributes($product));

                // get options
                $xProduct->setData('x_options', $this->getProductOptions()->getOptions($product));
                $xProduct->setData('customizable_options', $this->getProductOptions()->getCustomizableOptions($product));
                // get stock_items
                $xProduct->setData('stock_items', $this->getProductStock()->getStock($product));
                $items[] = $xProduct;
            }
        }
        return $this->getSearchResult()
            ->setItems($items)
            ->setTotalCount($collection->getSize());
    }

    public function getProductCollection(Varien_Object $searchCriteria)
    {
        $storeId = $this->getSearchCriteria()->getData('storeId');
        if (is_null($storeId)) {
            throw new Exception(__('Must have param storeId'));
        } else {
            $this->getStoreManager()->setCurrentStore($storeId);
        }

        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = $this->_productModel->getCollection();
        $collection->addAttributeToSelect('*');
        $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
        $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        $collection->addStoreFilter($storeId);
        $collection->setCurPage(is_nan($searchCriteria->getData('currentPage')) ? 1 : $searchCriteria->getData('currentPage'));
        $collection->setPageSize(
            is_nan($searchCriteria->getData('pageSize')) ? SM_XRetail_Helper_DataConfig::PAGE_SIZE_LOAD_PRODUCT : $searchCriteria->getData('pageSize')
        );
        if ($searchCriteria->getData('status')) {
            // 1: Enable/ 2: Disable
            $collection->addAttributeToFilter('status', ['in' => $searchCriteria->getData('status')]);
        }

        if ($searchCriteria->getData('visibility')) {
            // 1: Not Visible Individually / 2: Catalog / 3: Search / 4: Catalog, Search
            $collection->addAttributeToFilter('visibility', ['in' => $searchCriteria->getData('visibility')]);
        }

        if ($searchCriteria->getData('typeId')) {
            $collection->addAttributeToFilter('type_id', ['in' => $searchCriteria->getData('typeId')]);
        }

        if ($searchCriteria->getData('productIds')) {
            $collection->addFieldToFilter('entity_id', ['in' => $searchCriteria->getData('productIds')]);
        }
        return $collection;
    }

    /**
     * @return SM_Product_Model_ProductManagement_ProductOptions
     */

    public function getProductOptions()
    {
        return $this->productOptions;
    }

    /**
     * @return Mage_Catalog_Model_Product_Media_Config
     */

    public function getProductMediaConfig()
    {
        return $this->productMediaConfig;
    }

    /**
     * @return SM_Product_Model_ProductManagement_ProductAttribute
     */
    public function getProductAttribute()
    {
        return $this->productAttribute;
    }

    /**
     * @return SM_Product_Helper_CustomSales_Data
     */
    public function getCustomSalesHelper()
    {
        return $this->_customSalesHelper;
    }

    /**
     * @return SM_Product_Model_ProductManagement_ProductStock
     */
    public function getProductStock()
    {
        return $this->productStock;
    }

    /**
     * @return SM_Product_Model_ProductManagement_ProductPrice
     */
    public function getProductPrice()
    {
        return $this->productPrice;
    }

}