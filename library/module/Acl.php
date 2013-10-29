<?php

namespace Library\Module;

class Acl extends \Library\Module
{

    private $_groups = array();
    private $_group = null;

    public function addGroup($group, $parent = null)
    {
        if(isset($this->_groups[$parent])){
            $this->_groups[strval($group)] = $this->_groups[$parent];
            $this->_groups[strval($group)]['parent'] = $parent;
        } elseif(is_null($parent)) {
            $this->_groups[strval($group)] = array(
                'parent' => $parent,
                'allow'  => array(),
                'deny'   => array(),
                'mode'   => false
            );
        } else {
            \Library\Registry::getInstance()->log->writeWarning('Unknown group "' . $parent . '"! Cannot clone it!');
        }
        return $this;
    }

    public function allow($group, $controller = null, array $actions = null)
    {
        if(isset($this->_groups[$group])){
            if ($controller AND $actions) {
                $this->_groups[$group]['allow'][(string) $controller] = $actions;
            } elseif ($controller) {
                $this->_groups[$group]['allow'][(string) $controller] = true;
            } else {
                $this->_groups[$group]['mode'] = true;
            }
        } else {
            \Library\Registry::getInstance()->log->writeWarning('Unknown group "' . $group . '"! Cannot add rule!');
        }
        return $this;
    }

    public function deny($group, $controller = null, $actions = null)
    {
        if (isset($this->_groups[$group])) {
            if ($controller AND $actions) {
                $this->_groups[$group]['deny'][(string) $controller] = $actions;
            } elseif ($controller) {
                $this->_groups[$group]['deny'][(string) $controller] = true;
            } else {
                $this->_groups[$group]['mode'] = false;
            }
        } else {
            \Library\Registry::getInstance()->log->writeWarning('Unknown group "' . $group . '"! Cannot add rule!');
        }
        return $this;
    }

    public function setGroup($group)
    {
        $this->_group = (string) $group;
        return $this;
    }

    public function getGroup()
    {
        return $this->_group;
    }

    public function isAllowed()
    {
        if(!$this->getGroup()){
            \Library\Registry::getInstance()->log->writeError('Unknown group! Access disallowed!');
            return false;
        }
        $rules = $this->_groups[$this->getGroup()];
        $request = \Library\Registry::getInstance()->request;
        $controller = $request->getController();
        $action     = $request->getAction();
        if($rules['mode']){
            $flag = true;
            if($rules['deny'][$controller]){
                if(is_array($rules['deny'][$controller])){
                    if(in_array($action, $rules['deny'][$controller])){
                        $flag = false;
                    }
                } else {
                    if (is_array($rules['allow'][$controller])) {
                        if(!in_array($action, $rules['allow'][$controller])){
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
        if($flag){
            \Library\Registry::getInstance()->log->writeSuccess('Access allowed!');
        } else {
            \Library\Registry::getInstance()->log->writeError('Access disallowed!');
        }
        return $flag;
    }

}