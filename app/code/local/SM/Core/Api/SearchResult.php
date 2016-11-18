<?php

class SM_Core_Api_SearchResult extends SM_Core_Model_DataObject
{

    const TYPE_OUTPUT = 'underscore';
    protected $_cacheUnderScore = [];

    public function __construct(
        array $data = []
    ) {
        parent::__construct($data);
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->getData('items');
    }

    /**
     * @param array $items
     *
     * @return $this
     */
    public function setItems(array $items)
    {
        $this->setData('items', $items);

        return $this;
    }


    /**
     * @return mixed
     */
    public function getTotalCount()
    {
        return $this->getData('total_count');
    }

    /**
     * @param $totalCount
     *
     * @return $this
     */
    public function setTotalCount($totalCount)
    {
        $this->setData('total_count', $totalCount);

        return $this;
    }

    /**
     * Get Client Cache Key
     *
     * @return string|null
     */
    public function getClientKey()
    {
        return $this->getData('client_key');
    }

    /**
     * Set Client Cache Key
     *
     * @param string $clientKey
     *
     * @return $this
     */
    public function setClientKey($clientKey)
    {
        $this->setData('client_key', $clientKey);

        return $this;
    }


    /**
     * @return mixed
     */
    public function getSearchCriteria()
    {
        return $this->getData('search_criteria');
    }

    /**
     * @param $searchCriteria
     *
     * @return $this
     */
    public function setSearchCriteria($searchCriteria)
    {
        $this->setData('search_criteria', $searchCriteria->getData());

        return $this;
    }

    /**
     * @return array
     */
    public function getOutput()
    {
        $items = [];

        foreach ($this->getItems() as $item) {
            if ($item instanceof SM_Core_Api_Data_Contract_ApiDataAbstract) /* @var $item ApiDataAbstract */ {
                $items[] = $item->getOutput();
            } else {
                $items[] = $item;
            }
        }
        return $this->formatDataOutput(
            [
                'items' => $items,
                'search_criteria' => $this->getSearchCriteria(),
                'total_count' => $this->getTotalCount()
            ]);
    }

    /**
     * @param $array
     *
     * @return mixed
     */
    public function formatDataOutput($array)
    {
        if (self::TYPE_OUTPUT == 'underscore' && is_array($array)) {
            return $this->fixArrayKey($array);
        } else {
            return $array;
        }
    }

    /**
     * @param $arr
     *
     * @return array
     */
    protected function fixArrayKey($arr)
    {
        $php53 = $this;
        $arr = array_combine(
            array_map(
                function ($str) use ($php53) {
                    return $php53->_underscore($str);
                },
                array_keys($arr)),
            array_values($arr));
        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                $this->fixArrayKey($arr[$key]);
            }
        }
        return $arr;
    }

    /**
     * Converts field names for setters and geters
     *
     * $this->setMyField($value) === $this->setData('my_field', $value)
     * Uses cache to eliminate unneccessary preg_replace
     *
     * @param string $name
     *
     * @return string
     */
    public function _underscore($name)
    {
        if (!isset($this->_cacheUnderScore[$name])) {
            $this->_cacheUnderScore[$name] = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $name));
        }

        return $this->_cacheUnderScore[$name];
    }
}