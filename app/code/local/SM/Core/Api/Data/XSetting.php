<?php

/**
 * Created by PhpStorm.
 * User: thanhnt1@smartosc.com
 * Date: 11/8/16
 * Time: 6:11 PM
 */
class SM_Core_Api_Data_XSetting extends SM_Core_Api_Data_Contract_ApiDataAbstract
{
    public function getKey()
    {
        return $this->getData('key');
    }

    public function getValue(){
        return $this->getData('value');
    }
}