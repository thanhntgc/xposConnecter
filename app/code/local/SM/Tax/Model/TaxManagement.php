<?php

class SM_Tax_Model_TaxManagement extends SM_XRetail_Model_Api_ServiceAbstract
{
    /**
     * @var SM_Tax_Model_Calculation
     */
    protected $_taxCalculation;

    public function __construct()
    {
        $this->_taxCalculation = Mage::getModel('xtax/calculation');
        return parent::__construct();
    }

    public function getTaxRatesData()
    {
        if ($this->getSearchCriteria()->getData('currentPage') > 1) {
            return $this->getSearchResult()->setItems([])->getOutput();
        }
        return $this->loadTaxRates()->getOutput();
    }

    public function loadTaxRates()
    {
        $rates = $this->_taxCalculation->getRates();
        $items = [];

        foreach ($rates as $taxCalculationId => $rate) {
            $xrate = new SM_Core_Api_Data_XTaxRate();
            $xrate->addData($rate);
            $xrate->setData('tax_calculation_id', $taxCalculationId);
            $items[] = $xrate;
        }

        return $this->getSearchResult()
            ->setItems($items)
            ->setTotalCount(count($items));
    }
}