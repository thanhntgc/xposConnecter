<?php

/**
 * Class SM_Product_Model_ProductManagement_ProductOptions
 */
class SM_Product_Model_ProductManagement_ProductOptions
{
    /**
     * @var Mage_Adminhtml_Block_Catalog_Product_Composite_Fieldset_Configurable
     */
    protected $_configurableBlock;

    /**
     * @var SM_Product_Block_Catalog_Product_View_Type_Bundle
    //     * @var Mage_Bundle_Block_Adminhtml_Catalog_Product_Composite_Fieldset_Bundle
     */
    protected $_bundleBlock;

    /**
     * @var Mage_Catalog_Helper_Product
     */
    protected $catalogProduct;

    /**
     * @var SM_XRetail_Helper_Registry
     */
    protected $_coreRegistry;

    /**
     * @var Mage_Adminhtml_Block_Catalog_Product_Composite_Fieldset_Grouped
     */
    protected $_groupBlock;

    public function __construct()
    {
        $this->_configurableBlock = Mage::getBlockSingleton('catalog/product_view_Type_configurable');
        $this->_bundleBlock = Mage::getBlockSingleton('xproduct/Catalog_Product_View_Type_Bundle');
        $this->_groupBlock = Mage::getBlockSingleton('adminhtml/catalog_product_composite_fieldset_grouped');
        $this->catalogProduct = Mage::helper('catalog/product');
        $this->_coreRegistry = Mage::helper('xretail/registry');
    }

    public function getOptions(Mage_Catalog_Model_Product $product)
    {
        $xOptions = [];
        switch ($product->getTypeId()) {
            case 'configurable':
                $xOptions['configurable'] = $this->getOptionsConfigurableProduct($product);
                break;
            case 'bundle':
                $xOptions['bundle'] = $this->getOptionsBundleProduct($product);
                break;
            case 'grouped':
                $xOptions['grouped'] = $this->getAssociatedProducts($product);
                break;
        }

        return $xOptions;
    }

    public function getCustomizableOptions(Mage_Catalog_Model_Product $product)
    {
        return $this->getCustomOptionsSimpleProduct($product);
    }

    protected function getOptionsConfigurableProduct(Mage_Catalog_Model_Product $product)
    {
        $this->resetProductInBlock($product);

        return json_decode($this->_configurableBlock->getJsonConfig(), true);
    }

    /**
     * @param $product
     *
     * @return mixed
     */
    protected function getOptionsBundleProduct(Mage_Catalog_Model_Product $product)
    {
        $this->resetProductInBlock($product);
        $this->catalogProduct->setSkipSaleableCheck(true);
        $outputOptions = array();
        $options = Mage::helper('core')->decorateArray($this->_bundleBlock->resetOptions()->getOptions());

        foreach ($options as $option) {
            foreach ($option->getSelections() as $selection) {
                if (isset($outputOptions[$option->getTitle()])) {
                    $outputOptions[$option->getTitle()]['section'][] = ['product_id' => $selection->getId()];
                } else {
                    $outputOptions[$option->getTitle()] = [
                        'data' => $option->getData(),
                        'section' => [['product_id' => $selection->getId()]]
                    ];
                }
            }
        }

        return $outputOptions;
    }

    /**
     * @param $product
     *
     * @return array
     */
    protected function getAssociatedProducts(Mage_Catalog_Model_Product $product)
    {
        $outputOptions = [];
//        $this->_groupBlock->unsetData('product');
        $this->resetProductInBlock($product);
        $_associatedProducts = $this->_groupBlock->getAssociatedProducts();
        $_hasAssociatedProducts = count($_associatedProducts) > 0;
        if ($_hasAssociatedProducts) {
            foreach ($_associatedProducts as $_item) {
                $outputOptions[] = $_item->getData();
            }
        }

        return $outputOptions;
    }

    protected function getCustomOptionsSimpleProduct(Mage_Catalog_Model_Product $product)
    {
        $options = array();
        if ($product->getOptions()) {
            foreach ($product->getOptions() as $value) {
                $custom_option = $value->getData();
                $values = $value->getValues();
                $custom_option_data = array();
                if (is_array($values)) {
                    foreach ($values as $valuess) {
                        $custom_option_data[] = $valuess->getData();

                    }
                }

                $custom_option['data'] = $custom_option_data;
                $options = $custom_option;
            }
        }
        return $options;
    }

    private function resetProductInBlock($product)
    {
        $this->_coreRegistry->unregister('current_product');
        $this->_coreRegistry->unregister('product');
        $this->_coreRegistry->register('current_product', $product);
        $this->_coreRegistry->register('product', $product);

        return $this;
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::getModel('catalog/product');
    }

}