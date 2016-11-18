<?php

/**
 * Created by PhpStorm.
 * User: thanhnt1@smartosc.com
 * Date: 11/8/16
 * Time: 6:27 PM
 */
abstract class SM_Setting_Model_SettingManagement_AbstractSetting
{
    /**
     * @var Mage_Core_Model_Config
     */
    protected $scopeConfig;
    /**
     * @var
     */
    protected $store;


    protected $CODE = "default";

    /**
     * SM_Setting_Model_SettingManagement_AbstractSetting constructor.
     */
    public function __construct()
    {
        $this->scopeConfig = Mage::getConfig();
    }

    public function build()
    {
        return [];
    }

    public function getCODE()
    {
        return $this->CODE;
    }

    public function getScopeConfig()
    {
        return $this->scopeConfig;
    }

    public function getStore()
    {
        return $this->store;
    }

    public function setStore($store)
    {
        $this->store = $store;
    }
}