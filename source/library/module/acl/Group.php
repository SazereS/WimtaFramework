<?php

namespace Library\Module\Acl;

class Group {

    private $_name;
    private $_rules = array(
        'parent' => null,
        'allow'  => array(),
        'deny'   => array(),
        'mode'   => false
    );

    public function __construct($name)
    {
        $this->_name = (string) $name;
    }

    public function __clone()
    {
        $this->_rules['parent'] = $this->_name;
    }

    public function __toString()
    {
        return $this->_name;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function setName($name)
    {
        $this->_name = (string) $name;
        return $this;
    }

    public function allow($controller = null, array $actions = null)
    {
        if (!is_null($controller)) {
            $controller = (string) $controller;
        }
        if ($controller AND $actions) {
            if (is_array($this->_rules['allow'][$controller])) {
                $actions = array_merge($this->_rules['allow'][$controller], $actions);
            }
            $this->_rules['allow'][$controller] = $actions;
        } elseif ($controller) {
            $this->_rules['allow'][$controller] = true;
        } else {
            $this->_rules['mode'] = true;
        }
        return $this;
    }

    public function deny($controller = null, $actions = null)
    {
        if (!is_null($controller)) {
            $controller = (string) $controller;
        }
        if ($controller AND $actions) {
            if (is_array($this->_rules['deny'][$controller])) {
                $actions = array_merge($this->_rules['deny'][$controller], $actions);
            }
            $this->_rules['deny'][$controller] = $actions;
        } elseif ($controller) {
            $this->_rules['deny'][$controller] = true;
        } else {
            $this->_rules['mode'] = false;
        }
        return $this;
    }

    public function isAllowed()
    {
        $rules      = $this->_rules;
        $request    = \Library\Registry::getInstance()->request;
        $controller = $request->getController();
        $action     = $request->getAction();
        if ($rules['mode']) {
            $flag = true;
            if ($rules['deny'][$controller]) {
                if (is_array($rules['deny'][$controller])) {
                    if (in_array($action, $rules['deny'][$controller])) {
                        $flag = false;
                    }
                } else {
                    if (is_array($rules['allow'][$controller])) {
                        if (!in_array($action, $rules['allow'][$controller])) {
                            $flag = false;
                        }
                    } else {
                        $flag = false;
                    }
                }
            }
        } else {
            $flag = false;
            if ($rules['allow'][$controller]) {
                if (is_array($rules['allow'][$controller])) {
                    if (in_array($action, $rules['allow'][$controller])) {
                        $flag = true;
                    }
                } else {
                    if (is_array($rules['deny'][$controller])) {
                        if (!in_array($action, $rules['deny'][$controller])) {
                            $flag = true;
                        }
                    } else {
                        $flag = true;
                    }
                }
            }
        }
        if ($flag) {
            \Library\Registry::getInstance()->log->writeSuccess('Access for group "' . $this->_name . '" allowed!');
        } else {
            \Library\Registry::getInstance()->log->writeError('Access for group "' . $this->_name . '" disallowed!');
        }
        return $flag;
    }

}