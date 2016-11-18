<?php


class SM_XRetail_Model_Api_Configuration extends Mage_Core_Model_Abstract
{
    /**
     * @var Mage_Core_Model_Config
     */
    protected $_mageConfig;

    /**
     * @var return : array|null
     */
    protected $_apiRouters;

    /**
     * SM_XRetail_Model_Api_Configuration constructor.
     */
    public function __construct()
    {
        $this->_mageConfig = Mage::getConfig();
        parent::__construct();
    }

    /**
     * @return Mage_Core_Model_Config_Element
     */
    public function getApiRouters()
    {
        if (is_null($this->_apiRouters)) {
            $this->_apiRouters = $this->_mageConfig->getNode('apirouters/router');
        }
        return $this->_apiRouters;
    }

    /**
     * Get data config.xml
     * @param $path
     * @param string $scope
     * @param null $scopeCode
     * @return Mage_Core_Model_Config_Element
     */
    public function getNode($path, $scope = '', $scopeCode = null)  //path , scrope type , scopecode
    {
        return $this->_mageConfig->getNode($path, $scope, $scopeCode);
    }

    /**
     *  Set data config.xml
     * @param $path
     * @param $value
     * @param bool $overwrite
     * @return Varien_Simplexml_Config
     */
    public function setNode($path, $value, $overwrite = true)
    {
        return $this->_mageConfig->setNode($path, $value, $overwrite);
    }

    /**
     * @param $path
     * @param $string
     * @throws Exception
     */
    public function setConfig($path, $string)
    {
        try {
            Mage::getModel('core/config_data')
                ->load($path, 'path')
                ->setValue($string)
                ->setPath($path)
                ->save();
        } catch (Exception $e) {
            throw new Exception(Mage::helper('cron')->__('Unable to save the config data with path ' . $path));
        }
    }

    /**
     * @param $path
     * @return Mage_Core_Model_Config_Data
     */
    public function getConfig($path)
    {
        return Mage::getModel('core/config_data')->load($path, 'path');
    }

    public function getStoreConfig($path, $store = null)
    {
        return Mage::getStoreConfig($path, $store);
    }
}
