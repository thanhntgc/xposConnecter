<?php


class SM_XRetail_Helper_Registry extends Mage_Core_Helper_Abstract
{
    const PAGE_SIZE_LOAD_PRODUCT = 100;

    public function register($key, $value, $graceful = false)
    {
        Mage::register($key, $value, $graceful);
    }

    public function unregister($key)
    {
        Mage::unregister($key);
    }

    public function registry($key)
    {
        return Mage::registry($key);
    }
}