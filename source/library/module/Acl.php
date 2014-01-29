<?php

namespace Library\Module;

class Acl extends \Library\Module
{

    private $_groups = array();

    /**
     * Acl\Group
     */
    private $_group = null;

    /**
     *
     * @param string $group
     * @param string $parent
     * @return Acl\Group
     */
    public function addGroup($group, $parent = null)
    {
        if(is_null($parent)) {
            $this->_groups[$group] = new Acl\Group($group);
        } elseif(isset($this->_groups[(string) $parent])){
            $this->_groups[$group] = clone $this->_groups[(string) $parent];
            $this->_groups[$group]->setName($group);
        } else {
            \Library\Registry::getInstance()->log->writeWarning('Unknown group "' . $parent . '"! Cannot clone it!');
        }
        return $this->_groups[$group];
    }

    public function allow($group, $controller = null, array $actions = null)
    {
        if(isset($this->_groups[$group])){
            $this->_groups[$group]->allow($controller, $actions);
        } else {
            \Library\Registry::getInstance()->log->writeWarning('Unknown group "' . $group . '"! Cannot add rule!');
        }
        return $this;
    }

    public function deny($group, $controller = null, $actions = null)
    {
        if (isset($this->_groups[$group])) {
            $this->_groups[$group]->deny($controller, $actions);
        } else {
            \Library\Registry::getInstance()->log->writeWarning('Unknown group "' . $group . '"! Cannot add rule!');
        }
        return $this;
    }

    public function setGroup($group)
    {
        $this->_group = $this->_groups[$group];
        return $this;
    }

    /**
     * @return Acl\Group
     */
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
        return $this->getGroup()->isAllowed();
    }

}