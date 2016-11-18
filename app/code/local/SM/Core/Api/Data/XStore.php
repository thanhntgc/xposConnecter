<?php

class SM_Core_Api_Data_XStore extends SM_Core_Api_Data_Contract_ApiDataAbstract
{
    public function getId()
    {
        return $this->getData('store_id');
    }

    public function getCode()
    {
        return $this->getData('code');
    }

    public function getWebsiteId()
    {
        return $this->getData('website_id');
    }

    public function getGroupId()
    {
        return $this->getData('group_id');
    }

    public function getName()
    {
        return $this->getData('name');
    }

    public function getSortOrder()
    {
        return $this->getData('sort_order');
    }

    public function getIsActive()
    {
        return $this->getData('is_active');
    }

    public function getBaseCurrency()
    {
        return $this->getData('base_currency');
    }

    public function getCurrentCurrency()
    {
        return $this->getData('current_currency');
    }

    public function getRate()
    {
        return $this->getData('rate');
    }

    public function getPriceFormat()
    {
        return $this->getData('price_format');
    }

}