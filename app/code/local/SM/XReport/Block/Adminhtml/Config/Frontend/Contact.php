<?php

class SM_XReport_Block_Adminhtml_Config_Frontend_Contact extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    protected function _getHeaderCommentHtml($element)
    {
        $modules = Mage::getConfig()->getNode('modules')->children();
        $modulesArray = (array)$modules;
        if (isset($modulesArray['SM_XReport'])) {
            $version = $modulesArray['SM_XReport']->version;
        }
        $content = <<<TEXT
            <strong>Report Issue : <a href="http://support.smartosc.com/index.php?/Tickets/Submit/RenderForm/2" target="_blank" >Submit a ticket</a></br></strong>
            <strong>Send feedback : <a href="mailto:support@smartosc.com" >support@smartosc.com</a></strong>
            <p><strong>Verions: {$version}</strong></p>
TEXT;
        $element->setComment($content);
        return parent::_getHeaderCommentHtml($element);
    }

}
