<?php

class SM_Product_Model_ProductManagement_ProductPrice
{
    /**
     * @param Mage_Catalog_Model_Product $product
     * @param $key
     * @param bool|false $returnRawData
     * @return array|mixed|null
     * @throws Mage_Core_Exception
     */
    public function getExistingPrices(Mage_Catalog_Model_Product $product, $key, $returnRawData = false)
    {
        $prices = $product->getData($key);

        if ($prices === null) {
            $attribute = $product->getResource()->getAttribute($key);
            if ($attribute) {
                $attribute->getBackend()->afterLoad($product);
                $prices = $product->getData($key);
            }
        }

        if ($prices === null || !is_array($prices)) {
            return ($returnRawData ? $prices : []);
        }

        return $prices;
    }
}