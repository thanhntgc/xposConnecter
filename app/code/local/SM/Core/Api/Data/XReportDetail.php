<?php

/**
 * Created by PhpStorm.
 * User: thanhnt
 * Date: 18/11/2016
 * Time: 16:31
 */
class SM_Core_Api_Data_XReportDetail extends SM_Core_Api_Data_Contract_ApiDataAbstract
{
    public function __construct($data = array())
    {
        parent::_construct($data);
    }

    public function getMethod()
    {
        return Mage::helper('xreport')->getConfigDataPaymentMethod($this->getData('method'), 'title');
    }

    public function getStatus()
    {
        return $this->getData('status');
    }

    public function getShippingMethod(){
        return Mage::helper('xreport')->getCurrentShippingTitle($this->getData('shipping_method'));
    }

    public function getSku(){
        return $this->getData('sku');
    }
    public function getCustomerGroupCode(){
//        return $this->
    }
}