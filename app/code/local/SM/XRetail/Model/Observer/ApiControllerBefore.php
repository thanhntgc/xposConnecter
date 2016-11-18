<?php

class SM_XRetail_Model_Observer_ApiControllerBefore extends Mage_Core_Model_Abstract
{
    /**
     * @param Varien_Event_Observer $observer
     * @return mixed
     */
    public function checkPath(Varien_Event_Observer $observer)
    {
        $apiController = $observer->getData('apiController');
        // get data as json
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_null($data)) {
            $apiController->getRequest()->setParams($data);
        }

        return $apiController->checkPath();
    }
}