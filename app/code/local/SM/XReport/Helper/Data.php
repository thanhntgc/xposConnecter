<?php

/**
 * Created by PhpStorm.
 * User: thanhnt
 * Date: 18/11/2016
 * Time: 14:12
 */
class SM_XReport_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @param $p
     * @return mixed
     */
    public function _formatPrice($p)
    {
        $currentStoreId = Mage::app()->getStore()->getStoreId();
        if ($currentStoreId != 0)
            return Mage::helper('core')->currencyByStore(floatval($p), $currentStoreId, true, false);
        else
            return Mage::helper('core')->currency(floatval($p), true, false);
    }

    /**
     * @param $code
     * @param $field
     * @return mixed
     */
    public function getConfigDataPaymentMethod($code, $field)
    {
        $path = 'payment/' . $code . '/' . $field;

        return Mage::getStoreConfig($path);
    }

    public function getCurrentShippingTitle($_codeIn)
    {
        $methods = Mage::getSingleton('shipping/config')->getActiveCarriers();
        foreach ($methods as $_ccode => $_carrier) {
            if ($_methods = $_carrier->getAllowedMethods()) {
                foreach ($_methods as $_mcode => $_method) {
                    $_code = $_ccode . '_' . $_mcode;
                    if ($_codeIn == $_code) {
                        if (!$_title = Mage::getStoreConfig("carriers/$_mcode/title"))
                            return $_method;
                        else
                            return $_title;
                    }
                }
            }
        }

        return $_codeIn;
    }
}