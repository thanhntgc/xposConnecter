<?php


class SM_XRetail_Helper_DataConfig extends Mage_Core_Helper_Abstract
{

    const PAGE_SIZE_LOAD_PRODUCT = 100;
    const PAGE_SITE_LOAD_CUSTOMER = 200;
    const PAGE_SITE_LOAD_DATA = 200;

    /**
     * @return bool
     */
    public function getSupportCustomOptionsSimpleProduct()
    {
        return false;
    }

    public function getApiGetCustomAttributes()
    {
        return false;
    }

    public function getOrderCreateAllowEvent()
    {
        return false;
    }

    /**
     * Cách tính discount per item.
     * True: Tính discount ammount theo tỷ trọng
     * False: Trừ theo thứ tự
     *
     * @return bool
     */
    public function calculateDiscountByProportion()
    {
        return true;
    }
}