<?php

class SM_Product_Model_ProductManagement_ProductStock
{
    /**
     * @var Mage_CatalogInventory_Model_Resource_Stock_Item
     */
    protected $_stockModel;

    public function __construct()
    {
        $this->_stockModel = Mage::getResourceModel('cataloginventory/stock_item');
    }

    public function getStock($product)
    {
        if (!!($itemData = $product->getData()) && isset($itemData['stock_item']) && $itemData['stock_item'] instanceof Mage_CatalogInventory_Model_Stock_Item) {
            return $itemData['stock_item']->getData();
        }

        $inventoryModel = Mage::getModel('cataloginventory/stock_item');
        $this->_stockModel->loadByProductId($inventoryModel, $product->getId());
        return $inventoryModel;
    }

}