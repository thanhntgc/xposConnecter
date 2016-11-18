<?php

/**
 * Created by PhpStorm.
 * User: thanhnt1@smartosc.com
 * Date: 11/8/16
 * Time: 6:23 PM
 */
class SM_Setting_Model_SettingManagement_Shipping extends SM_Setting_Model_SettingManagement_AbstractSetting implements SM_Setting_Model_SettingManagement_SettingInterface
{
    protected $CODE = 'shipping';

    public function build()
    {
        return [
            'country_id' => $this->getScopeConfig()->getNode(
                Mage_Shipping_Model_Config::XML_PATH_ORIGIN_COUNTRY_ID,
                'store', intval($this->getStore())
            )->asArray(),
            'region_id' => $this->getScopeConfig()->getNode(
                Mage_Shipping_Model_Config::XML_PATH_ORIGIN_REGION_ID,
                'store', intval($this->getStore()))->asArray(),
            'postcode' => $this->getScopeConfig()->getNode(
                Mage_Shipping_Model_Config::XML_PATH_ORIGIN_POSTCODE,
                'store', intval($this->getStore()))->asArray()
        ];
    }
}