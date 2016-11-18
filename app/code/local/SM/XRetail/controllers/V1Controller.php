<?php
require_once(BP . DS . 'app' . DS . 'code' . DS . 'local' . DS . 'SM' . DS . 'XRetail' . DS . 'controllers' . DS . 'Contract' . DS . 'ApiAbstract.php');

/**
 * Class SM_XRetail_V1Controller
 */
class SM_XRetail_V1Controller extends SM_XRetail_Contract_ApiAbstract
{

    /**
     * Rest API
     *
     * @return Zend_Controller_Response_Abstract
     */
    public function XretailAction()
    {
        try {
            // authenticate
//            $this->authentication->authenticate($this);
            // communicate with api before
            $this->dispatchEvent('rest_api_before', ['apiController' => $this]);
            // call service
            $this->setOutput(
                call_user_func_array(
                    [$this->getService(), $this->getFunction()],
                    $this->getRequest()->getParams()));
            // communicate with api after
            $this->dispatchEvent('rest_api_after', ['apiController' => $this]);

            // output data
            return $this->jsonOutput();

        } catch (Exception $e) {
            return $this->outputError($e->getMessage(), $this->getStatusCode());
        }
    }

//    public function authenticateAction()
//    {
//        try {
//            //check license key
//            $this->setOutput($this->authentication->getBlackHole($this));
//
//            return $this->jsonOutput();
//        } catch (Exception $e) {
//            return $this->outputError($e->getMessage(), $this->getStatusCode());
//        }
//    }
//
//    public function testAction()
//    {
//        if ($key = $this->getRequest()->getParams()) {
//            Mage::app()->getCache()->save(json_encode($key), 'real_time', array('abc'), 99999);
//            echo 'Saved data';
//        } else {
//            echo Mage::app()->getCache()->load('real_time');
//        }
//    }
}
