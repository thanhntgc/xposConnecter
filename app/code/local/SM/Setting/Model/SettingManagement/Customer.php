<?php

/**
 * Created by PhpStorm.
 * User: thanhnt1@smartosc.com
 * Date: 11/8/16
 * Time: 6:23 PM
 */
class SM_Setting_Model_SettingManagement_Customer extends SM_Setting_Model_SettingManagement_AbstractSetting implements SM_Setting_Model_SettingManagement_SettingInterface
{

    /**
     * @var string
     */
    protected $CODE = 'customer';

    /**
     * @var Mage_Customer_Model_Group
     */
    protected $customerGroupManagement;

    public function __construct()
    {
        $this->customerGroupManagement = Mage::getModel('customer/group');
        parent::__construct();
    }

    public function build()
    {
        return [
            'default_customer_tax_class' => $this->customerGroupManagement->getTaxClassId($this->getDefaultCustomerGroupId(intval($this->getStore()))),
            'not_logged_In_customer_tax_class' => $this->customerGroupManagement->getTaxClassId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID)
        ];
    }

    /**
     * @param null $store
     * @return int
     */
    public function getDefaultCustomerGroupId($store = null)
    {
        return (int)Mage::getStoreConfig(Mage_Customer_Model_Group::XML_PATH_DEFAULT_ID, $store);
    }
}