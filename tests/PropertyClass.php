<?php
class PropertyClass {
    private $_prop;

    public function __construct($prop) {
        $this->_prop = $prop;
    }
    public function __get($name) {
        if($name == 'property') {
            return $this->_prop;
        }
        return null;
    }
    
    public function __isset($name) {
        return $name == 'property';
    }
};
