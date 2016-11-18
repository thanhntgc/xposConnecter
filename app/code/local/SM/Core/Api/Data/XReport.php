<?php

/**
 * Created by PhpStorm.
 * User: thanhnt
 * Date: 18/11/2016
 * Time: 12:08
 */
class SM_Core_Api_Data_XReport extends SM_Core_Api_Data_Contract_ApiDataAbstract
{
    public function __construct($data = array())
    {
        parent::__construct($data);
    }

    public function getIncrementId()
    {
        return $this->getData('increment_id');
    }

    public function getCreatedAt()
    {
        return Mage::getSingleton('core/date')->date(null, $this->getData('created_at'));
    }

    public function getStatus()
    {
        return $this->getData('status');
    }

    public function getShippingMethod()
    {
        return Mage::helper('xreport')->getCurrentShippingTitle($this->getData('shipping_method'));
    }

    public function getShippingCode()
    {
        return $this->getData('shipping_method');
    }

    public function getGrandTotal()
    {
        return Mage::helper('xreport')->_formatPrice($this->getData('grand_total'));
    }

    public function getShippingAmount()
    {
        return Mage::helper('xreport')->_formatPrice($this->getData('shipping_amount'));
    }

    public function getMethod()
    {
        return Mage::helper('xreport')->getConfigDataPaymentMethod($this->getData('method'), 'title');
    }

    public function getMethodCode()
    {
        return $this->getData('method');
    }

    public function getBaseSubtotal()
    {
        return Mage::helper('xreport')->_formatPrice($this->getData('base_subtotal'));
    }
}