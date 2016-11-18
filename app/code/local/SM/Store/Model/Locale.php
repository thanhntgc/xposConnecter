<?php

class SM_Store_Model_Locale extends Mage_Core_Model_Locale
{
    /**
     * @var Mage_Directory_Model_Currency
     */
    protected $_currency;

    /**
     * SM_Store_Model_Locale constructor.
     */
    public function __construct()
    {
        $this->_currency = Mage::getModel('directory/currency');
    }

    /**
     * @param null $currencyCode
     * @return array
     */
    public function getPriceFormat($currencyCode = null)
    {
        if ($currencyCode) {
            $currency = $this->_currency->load($currencyCode);
        } else {
            $currency = Mage::app()->getStore()->getCurrentCurrency();
        }
        $format = Zend_Locale_Data::getContent($this->getLocaleCode(), 'currencynumber');
        $symbols = Zend_Locale_Data::getList($this->getLocaleCode(), 'symbols');

        $pos = strpos($format, ';');
        if ($pos !== false) {
            $format = substr($format, 0, $pos);
        }
        $format = preg_replace("/[^0\#\.,]/", "", $format);
        $totalPrecision = 0;
        $decimalPoint = strpos($format, '.');
        if ($decimalPoint !== false) {
            $totalPrecision = (strlen($format) - (strrpos($format, '.') + 1));
        } else {
            $decimalPoint = strlen($format);
        }
        $requiredPrecision = $totalPrecision;
        $t = substr($format, $decimalPoint);
        $pos = strpos($t, '#');
        if ($pos !== false) {
            $requiredPrecision = strlen($t) - $pos - $totalPrecision;
        }
        $group = 0;
        if (strrpos($format, ',') !== false) {
            $group = ($decimalPoint - strrpos($format, ',') - 1);
        } else {
            $group = strrpos($format, '.');
        }
        $integerRequired = (strpos($format, '.') - strpos($format, '0'));

        $result = array(
            'pattern' => $currency->getOutputFormat(),
            'precision' => $totalPrecision,
            'requiredPrecision' => $requiredPrecision,
            'decimalSymbol' => $symbols['decimal'],
            'groupSymbol' => $symbols['group'],
            'groupLength' => $group,
            'integerRequired' => $integerRequired
        );

        return $result;
    }
}