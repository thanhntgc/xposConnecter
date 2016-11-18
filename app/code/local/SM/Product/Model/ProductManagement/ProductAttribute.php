<?php

/**
 * Class SM_Product_Model_ProductManagement_ProductAttribute
 */
class SM_Product_Model_ProductManagement_ProductAttribute
{
    /**
     * @var
     */
    protected $_data;
    /**
     * @var Mage_Catalog_Model_Resource_Product_Attribute_Collection
     */
    protected $productAttributeCollection;
    /**
     * @var SM_XRetail_Helper_DataConfig
     */
    private $dataConfig;

    /**
     * ProductAttribute constructor.
     *
     * @param Mage_Catalog_Model_Resource_Product_Attribute_Collection $productAttributeCollection
     */
    public function __construct()
    {
        $this->dataConfig = Mage::helper('xretail/dataConfig');
        $this->productAttributeCollection = Mage::getModel('catalog/resource_product_attribute_collection');
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return array
     */
    public function getCustomAttributes(Mage_Catalog_Model_Product $product)
    {
        if ($this->dataConfig->getApiGetCustomAttributes()) {
            $customAtt = [];
                $attributes = $this->getAllCustomAttributes();
            foreach ($attributes as $attribute) {
                $val = $product->getData($attribute['value']);
                if (!is_null($val)) {
                    $customAtt[$attribute['value']] = $val;
                }
            }

            return $customAtt;
        } else {
            return [];
        }
    }

    /**
     * @return mixed
     */
    public function getAllCustomAttributes()
    {
        $result = [];
        $attributes = $this->productAttributeCollection
            ->addVisibleFilter();
        if ($attributes != null && $attributes->count() > 0) {
            foreach ($attributes as $attribute) {
                $result[] = ['value' => $attribute->getAttributeCode(), 'key' => $attribute->getFrontendLabel()];
            }
        }

        return $result;
    }
}