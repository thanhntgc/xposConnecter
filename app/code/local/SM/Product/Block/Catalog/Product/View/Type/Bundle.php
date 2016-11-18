<?php

class SM_Product_Block_Catalog_Product_View_Type_Bundle extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle
{
    /**
     * @return $this
     */
    public function resetOptions()
    {
        $this->_options = null;
        return $this;
    }
}