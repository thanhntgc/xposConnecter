<?php

/**
 * Created by PhpStorm.
 * User: thanhnt1@smartosc.com
 * Date: 11/8/16
 * Time: 6:23 PM
 */
class SM_Setting_Model_SettingManagement_Tax extends SM_Setting_Model_SettingManagement_AbstractSetting implements SM_Setting_Model_SettingManagement_SettingInterface
{
    /**
     * @var Mage_Tax_Model_Config
     */
    protected $taxConfig;

    /**
     * @var string
     */
    protected $CODE = "tax";

    public function __construct()
    {
        $this->taxConfig = Mage::getModel('tax/config');
        parent::__construct();
    }

    /**
     * @return false|Mage_Core_Model_Abstract|Mage_Tax_Model_Config
     */
    public function getTaxConfig()
    {
        return $this->taxConfig;
    }

    public function build()
    {
        $storeid = intval($this->getStore());
        return [
            'country' => $this->getScopeConfig()->getNode(
                Mage_Tax_Model_Config::CONFIG_XML_PATH_DEFAULT_COUNTRY,
                'store', $storeid
            )->asArray(),
            'region_id' => $this->getScopeConfig()->getNode(
                Mage_Tax_Model_Config::CONFIG_XML_PATH_DEFAULT_REGION,
                'store', $storeid
            )->asArray(),
            'postcode' => $this->getScopeConfig()->getNode(
                Mage_Tax_Model_Config::CONFIG_XML_PATH_DEFAULT_POSTCODE,
                'store', $storeid
            )->asArray(),
            'price_includes_tax' => $this->getTaxConfig()->priceIncludesTax($storeid),
            'discount_tax' => $this->getTaxConfig()->discountTax($storeid),
            'calculation_sequence' => $this->getTaxConfig()->getCalculationSequence($storeid),
            'shipping_tax_class' => $this->getTaxConfig()->getShippingTaxClass($storeid),
            'shipping_price_includes_tax' => $this->getTaxConfig()->shippingPriceIncludesTax($storeid),
            'cross_border_trade_enabled' => $this->getTaxConfig()->crossBorderTradeEnabled($storeid)
        ];
    }
}