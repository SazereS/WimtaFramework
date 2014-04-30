<?php

namespace Library\Module\BootstrapForm;

class Element
{

    protected $_template      = '<element %1$s>%2$s</element>';
    protected $_classes = '';
    protected $_attributes = array();
    protected $_validators = array();
    protected $_value = '';
    protected $_value_protect = false;
    protected $_name = '';

    public $custom_holder = false;

    public function __construct($name)
    {
        $this->_name = $name;
    }

    public function setAttribute($key, $value){
        $this->_attributes[$key] = $value;
        return $this;
    }

    public function setAttributes($attributes)
    {
        if(is_array($attributes)){
            foreach ($attributes as $k => $v){
                $this->setAttribute($k, $v);
            }
        } else {
            throw new \Library\Module\Exception('Array needs to set attributes!');
        }
        return $this;
    }

    public function getAttribute($key){
        return $this->_attributes[$key];
    }

    public function removeAttribute($key){
        unset($this->_attributes[$key]);
        return $this;
    }

    public function clearAttributes()
    {
        $this->_attributes = array();
        return $this;
    }

    public function setValue($value)
    {
        $value = (string) $value;
        if($this->_value_protect){
            $value = addcslashes($value, $this->_value_protect);
        }
        $this->_value = $value;
        return $this;
    }

    public function getValue()
    {
        return $this->_value;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function addValidator($validator, $params = array())
    {
        $this->_validators[] = array($validator, $params);
        return $this;
    }

    public function validate()
    {
        $errors = array();
        $validator_obj = \Library\Base::getModule('validator');
        foreach ($this->_validators as $validator){
            if(
                !call_user_func_array(
                    array(
                        $validator_obj,
                        'validate' . \Library\Base::getRealName($validator[0])
                    ),
                    array_merge(array($this->getValue()), $validator[1])
                )
            ){
                $errors[] = $validator[0];
            }
        }
        return $errors;
    }

    public function render()
    {
        $attrs_clone = $this->_attributes;
        if($this->_classes){
            $attrs_clone['class'] = $this->_classes . ' ' .$attrs_clone['class'];
        }
        if(!isset($attrs_clone['id'])){
            $attrs_clone['id'] = 'input-' . $this->_name;
        }
        $attrs = array();
        $attrs_clone['name'] = $this->_name;
        foreach ($attrs_clone as $attr => $val){
            $attrs[] = $attr . '="' . addcslashes($val, '"') . '"';
        }
        return sprintf($this->_template, implode(' ', $attrs), $this->_value, $this->getAttribute('label'));
    }

}
