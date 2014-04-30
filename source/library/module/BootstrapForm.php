<?php

namespace Library\Module;

/**
 * Description of BootstrapForm
 *
 * @author SazereS
 */
class BootstrapForm extends \Library\Module
{

    const FORM_HORIZONTAL = 'form-horizontal';
    const FORM_VERTICAL   = '';

    /**
     * @var \Library\Module\BootstrapForm\Element[]
     */
    protected $_elements = array();
    /**
     * @var array[]
     */
    protected $_validating_errors = array();
    protected $_rendered = '';
    protected $_element_holder =
'<div class="form-group%5$s">
    <label for="input-%3$s" class="col-md-%6$s control-label">%4$s</label>
    <div class="col-md-%7$s">
        %1$s
        %2$s
    </div>
</div>';
    protected $_form_holder = '<form %2$s>%1$s</form>';
    protected $_attributes = array(
        'method' => 'POST'
    );
    protected $_label_width   = 2;
    protected $_element_width = 10;

    public function __construct()
    {
        parent::__construct();
        // Some additional code here
    }

    public function __toString()
    {
        return ($this->_rendered) ? $this->_rendered : $this->render()->_rendered;
    }

    public function setAttribute($key, $value)
    {
        $this->_attributes[$key] = $value;
        return $this;
    }

    public function setAttributes($attributes)
    {
        if (is_array($attributes)) {
            foreach ($attributes as $k => $v) {
                $this->setAttribute($k, $v);
            }
        } else {
            throw new \Library\Module\Exception('Array needs to set attributes!');
        }
        return $this;
    }

    public function getAttribute($key)
    {
        return $this->_attributes[$key];
    }

    public function removeAttribute($key)
    {
        unset($this->_attributes[$key]);
        return $this;
    }

    public function clearAttributes()
    {
        $this->_attributes = array();
        return $this;
    }

    public function setLabelWidth($width = 2)
    {
        $this->_label_width = (int) $width;
        return $this;
    }

    public function setElementWidth($width = 10)
    {
        $this->_element_width = (int) $width;
        return $this;
    }

    /**
     *
     * @param string $type
     * @param string $name
     * @return \Library\Module\BootstrapForm\Element
     */
    public function addElement($name, $type, $attributes = array())
    {
        $class = '\\Library\\Module\\BootstrapForm\\Element\\' . \Library\Base::getRealName($type);
        $this->_elements[$name] = new $class($name);
        $this->_elements[$name]->setAttributes($attributes);
        return $this->_elements[$name];
    }

    public function setValues($array)
    {
        foreach ($array as $name => $value){
            if($this->_elements[$name]){
                if($this->_elements[$name] instanceof \Library\Module\BootstrapForm\Element\Checkbox){
                    $this->_elements[$name]->setAttribute('checked', 'checked');
                    continue;
                }
                $this->_elements[$name]->setValue($value);
            }
        }
        return $this;
    }

    public function validate($fill_errors = true)
    {
        $errors = array();
        foreach ($this->_elements as $name => $el){
            $e = $el->validate();
            if(!empty($e)){
                $errors[$name] = $e;
            }
        }
        $this->_validating_errors = $errors;
        return (empty($errors)) ? true : false;
    }

    public function render(){
        $elements = array();
        foreach ($this->_elements as $el){
            $elements[] = sprintf(
                (($el->custom_holder) ? $el->custom_holder : $this->_element_holder),
                $el->render(),
                ($this->_validating_errors[$el->getName()])
                    ? '<ul><li class="text-danger">' . implode('</li><li class="text-danger">', $this->_validating_errors[$el->getName()]) . '</li></ul>'
                    : '',
                $el->getName(),
                ($el->getAttribute('label')) ? $el->getAttribute('label') : '',
                ($this->_validating_errors[$el->getName()])
                    ? ' has-error'
                    : '',
                $this->_label_width,
                $this->_element_width
            );
        }
        $elements = implode('', $elements);
        $attrs_clone          = $this->_attributes;
        $attrs_clone['class'] = self::FORM_HORIZONTAL . ' ' . $attrs_clone['class'];
        if($attrs_clone['title']){
            $elements = '<legend>' . $attrs_clone['title'] . '</legend>' . $elements;
        }
        $attrs = array();
        foreach ($attrs_clone as $attr => $v){
            $attrs[] = $attr . '="' . addcslashes($v, '"') . '"';
        }
        $this->_rendered = sprintf(
            $this->_form_holder,
            $elements,
            implode(' ', $attrs)
        );
        return $this;
    }

}
