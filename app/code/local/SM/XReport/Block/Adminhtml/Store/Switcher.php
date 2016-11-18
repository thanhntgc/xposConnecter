<?php

/**
 * Created by IntelliJ IDEA.
 * User: vjcspy
 * Date: 10/21/15
 * Time: 4:17 PM
 */
class SM_XReport_Block_Adminhtml_Store_Switcher extends Mage_Adminhtml_Block_Store_Switcher {

    public function hasXpos()
    {
        $modules = Mage::getConfig()->getNode('modules')->children();
        $modulesArray = (array)$modules;

        if (isset($modulesArray['SM_XPos'])) {
            return (object) array('show' => true, 'value' => Mage::getSingleton('xreport/session')->getData('orderXpos'));
        }

        return (object) array('show' => false, 'value' => null);
    }

}
