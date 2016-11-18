<?php

/**
 * Created by PhpStorm.
 * User: thanhnt
 * Date: 16/11/2016
 * Time: 16:31
 */
class SM_Core_Api_Data_XCategory extends SM_Core_Api_Data_Contract_ApiDataAbstract
{

    public function getId() {
        return $this->getData('entity_id');
    }

    public function getParentId() {
        return $this->getData('parent_id');
    }

    public function getProductIds() {
        return $this->getData('product_ids');
    }

    public function getIsActive() {
        return $this->getData('is_active');
    }

    public function getLevel() {
        return $this->getData('level');
    }

    public function getPosition() {
        return $this->getData('position');
    }

    public function getPath() {
        return $this->getData('path');
    }
}