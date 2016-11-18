<?php

abstract class SM_Core_Api_Data_Contract_ApiDataAbstract extends SM_Core_Model_DataObject
{

    /**
     * get Method
     */
    const GET_METHOD = 'get';
    /**
     * @var []
     */
    protected $_dataOutput;

    /**
     * @var []
     */
    protected $_allGetApiMethod;

    /**
     * Data as array
     *
     * @return array
     */
    public function getOutput()
    {
        if (is_null($this->_dataOutput)) {
            $methods = $this->getAllGetApiMethod();
            foreach ($methods as $method) {
                if (substr($method, 0, 3) === self::GET_METHOD) {
                    $key = $this->_underscore(substr($method, 3));
                    $this->_dataOutput[$key] = call_user_func_array([$this, $method], []);
                    if ($this->_dataOutput[$key] instanceof SM_Core_Api_Data_Contract_ApiDataAbstract) {
                        $this->_dataOutput[$key] = $this->_dataOutput[$key]->getOutput();
                    }
                }
            }
        }

        return $this->_dataOutput;
    }

    /**
     * @return array get method
     */
    public function getAllGetApiMethod()
    {
        if (is_null($this->_allGetApiMethod)) {
            $class = new ReflectionClass(get_class($this));
            $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $method) {
                if ($method->getDeclaringClass()->getName() == get_class($this)) {
                    $this->_allGetApiMethod[] = $method->getName();
                }
            }
        }
        return $this->_allGetApiMethod;
    }
}