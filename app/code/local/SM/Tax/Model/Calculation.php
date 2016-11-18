<?php

class SM_Tax_Model_Calculation extends Mage_Tax_Model_Resource_Calculation
{
    public function getRates()
    {

        // Make SELECT and get data
        $select = $this->_getReadAdapter()->select();
        $select->from(array('main_table' => $this->getMainTable()),
            array(
                'tax_calculation_rate_id',
                'tax_calculation_rule_id',
                'customer_tax_class_id',
                'product_tax_class_id'
            )
        );
        $ifnullTitleValue = $this->_getReadAdapter()->getCheckSql(
            'title_table.value IS NULL',
            'rate.code',
            'title_table.value'
        );
        $ruleTableAliasName = $this->_getReadAdapter()->quoteIdentifier('rule.tax_calculation_rule_id');
        $select->join(
            array('rule' => $this->getTable('tax_calculation_rule')),
            $ruleTableAliasName . ' = main_table.tax_calculation_rule_id',
            array('rule.priority', 'rule.position', 'rule.calculate_subtotal')
        )->join(
            array('rate' => $this->getTable('tax_calculation_rate')),
            'rate.tax_calculation_rate_id = main_table.tax_calculation_rate_id',
            array(
                'value' => 'rate.rate',
                'rate.tax_country_id',
                'rate.tax_region_id',
                'rate.tax_postcode',
                'rate.tax_calculation_rate_id',
                'rate.code'
            )
        );

        /*FIXME: Not yet support Title*/
        //    ->joinLeft(
        //    ['title_table' => $this->getTable('tax_calculation_rate_title')],
        //    "rate.tax_calculation_rate_id = title_table.tax_calculation_rate_id " .
        //    "AND title_table.store_id = '{$storeId}'",
        //    ['title' => $ifnullTitleValue]
        //)

        $select->order('priority ' . Varien_Db_Select::SQL_ASC)
            ->order('tax_calculation_rule_id ' . Varien_Db_Select::SQL_ASC)
            ->order('tax_country_id ' . Varien_Db_Select::SQL_DESC)
            ->order('tax_region_id ' . Varien_Db_Select::SQL_DESC)
            ->order('tax_postcode ' . Varien_Db_Select::SQL_DESC)
            ->order('value ' . Varien_Db_Select::SQL_DESC);

        $fetchResult = $this->_getReadAdapter()->fetchAll($select);
        $filteredRates = [];
        if ($fetchResult) {
            foreach ($fetchResult as $rate) {
                if (!isset($filteredRates[$rate['tax_calculation_rate_id']])) {
                    $filteredRates[$rate['tax_calculation_rate_id']] = $rate;
                }
            }
        }
        return array_values($filteredRates);
    }
}