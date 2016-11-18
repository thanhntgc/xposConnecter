<?php

/**
 * Created by PhpStorm.
 * User: thanhnt1@smartosc.com
 * Date: 11/8/16
 * Time: 6:15 PM
 */
class SM_Setting_Model_SettingManagement extends SM_XRetail_Model_Api_ServiceAbstract
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getSettingData()
    {
        $settings = [];
        if ($this->getSearchCriteria()->getData('currentPage') != 1) {
        } else {
            foreach ($this->getSettingEntityCollection() as $item) {
                /** @var SM_Setting_Model_SettingManagement_AbstractSetting $instance */
                $instance = new $item();
                $store = $this->getSearchCriteria()->getData('storeId');
                if (!$store) {
                    throw new  Exception("Must have param storeId");
                }
                $allStores = Mage::app()->getStores();
                $_storeIds = array();
                foreach ($allStores as $dataStores => $val) {
                    $_storeId = Mage::app()->getStore($dataStores)->getId();
                    $_storeIds[] = $_storeId;
                }
                if (!in_array($store, $_storeIds)) {
                    throw  new Exception("Requested store is not found");
                }
                $instance->setStore($store);
                $setting = new SM_Core_Api_Data_XSetting();
                $setting->setData('key', $instance->getCODE());
                $setting->setData('value', $instance->build());
                $settings[] = $setting;
            }
        }
        return $this->getSearchResult()
            ->setSearchCriteria($this->getSearchCriteria())
            ->setItems($settings)
            ->getOutput();
    }

    /**
     * @return array
     */
    protected function getSettingEntityCollection()
    {
        return [
            'SM_Setting_Model_SettingManagement_Tax',
            'SM_Setting_Model_SettingManagement_Shipping',
            'SM_Setting_Model_SettingManagement_Customer',
        ];
    }
}