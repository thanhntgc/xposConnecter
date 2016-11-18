<?php

/**
 * Created by IntelliJ IDEA.
 * User: vjcspy
 * Date: 11/2/15
 * Time: 2:10 PM
 */
class SM_XReport_XMainController extends Mage_Adminhtml_Controller_Action {
    protected $_publicActions = array('index', 'switchStore', 'orderWithXpos', 'testSession');

    public function switchStoreAction() {
        $storeId = $this->getRequest()->getParam('store_id');
        if ($storeId == '')
            $storeId = 0;
        $this->getMainSession()->setData('store_id', $storeId);
        $this->getResponse()->setBody(json_encode(array(
            'result' => true,
            'store_id' => Mage::getSingleton('xreport/session')->getData('store_id')
        )));
    }

    public function orderWithXposAction() {
        $withXpos = $this->getRequest()->getParam('xpos', false);

        $modules = Mage::getConfig()->getNode('modules')->children();
        $modulesArray = (array)$modules;

        if (isset($modulesArray['SM_XPos'])) {
            $this->getMainSession()->setData('orderXpos', ($withXpos == false)?'false':$withXpos);
        }
        $this->getResponse()->setBody('true');
    }

    public function testSessionAction() {
        if (is_null(Mage::getSingleton('xreport/session')->getData('test_data'))) {
            Mage::getSingleton('xreport/session')->setData('test_data', 'test Data');
        }
        echo Mage::getSingleton('xreport/session')->getData('test_data');
    }

    private function getMainSession() {
        return Mage::getSingleton('xreport/session');
    }
}
