<?php


class SM_Core_Api_Data_XCustomer extends SM_Core_Api_Data_Contract_ApiDataAbstract
{

    protected $customer;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    public function getId()
    {
        return $this->getData('entity_id');
    }

    public function getCustomerGroupId()
    {
        return $this->getData('group_id');
    }

    public function getDefaultBilling()
    {
        return $this->getData('default_billing');
    }

    public function getDefaultShipping()
    {
        return $this->getData('default_shipping');
    }

    public function getEmail()
    {
        return $this->getData('email');
    }

    public function getFirstName()
    {
        return $this->getData('firstname');
    }

    public function getLastName()
    {
        return $this->getData('lastname');
    }

    public function getGender()
    {
        return $this->getData('gender');
    }

    public function getStoreId()
    {
        return $this->getData('store_id');
    }

    public function getWebsiteId()
    {
        return $this->getData('website_id');
    }

    public function getAddress()
    {
        return $this->getData('address');
    }

    public function getTaxClassId()
    {
        return $this->getData('tax_class_id');
    }
}