<?php

namespace Library\Module;

class Validator extends \Library\Module
{

    private $_regex = array(
        'int'   => '/^[0-9]+$/',
        'float' => '/^[0-9]+(,|\.)?[0-9]*$/',
        'phone' => '/^\+?[0-9]{0,4} ?\(?([0-9]{3})\)?([ .-]?)([- 0-9]{2,15})$/',
        'login' => '/^[-_a-zA-Z0-9]+$/',
        'alpha' => '/^[a-zA-Z0-9]+$/',
        'url'   => '|^https?://([\w-]+\.)+[\w]{2,4}(:[0-9]{1,5})?(/[\w-._%]+)*(\?.*)?(#[\w-.]*)?/?$|iu',
        'email' => '|^[\w-.]+@([\w-]+\.)+[\w]{2,4}$|iu',
        'ipv4'  => '/^(\d{1,2}|1[0-9]{2}|2[0-4]{1}[0-9]{1}|25[0-5]{1}){1}(\.(\d{1,2}|1[0-9]{2}|2[0-4]{1}[0-9]{1}|25[0-5]{1})){3}$/',
        'slug'  => '/^[\w ]+$/u',
        'text'  => '/^[\w ,\.!\?():;&-]+$/u'
    );

    public function __construct()
    {
        parent::__construct();
        \Library\Base::registerHelper('validator', function(){
            return $this;
        });
    }

    public function getRegEx($key)
    {
        return $this->_regex[$key];
    }

    public function validateRegEx($data, $regex)
    {
        if (preg_match($regex, $data)) {
            return true;
        }
        return false;
    }

    public function validateInt($data)
    {
        return $this->validateRegEx($data, $this->_regex['int']);
    }

    public function validateFloat($data)
    {
        return $this->validateRegEx($data, $this->_regex['float']);
    }

    public function validatePhone($data)
    {
        return $this->validateRegEx($data, $this->_regex['phone']);
    }

    public function validateLogin($data)
    {
        return $this->validateRegEx($data, $this->_regex['login']);
    }

    public function validateAlpha($data)
    {
        return $this->validateRegEx($data, $this->_regex['alpha']);
    }

    public function validateUrl($data)
    {
        return $this->validateRegEx($data, $this->_regex['url']);
    }

    public function validateEmail($data)
    {
        return $this->validateRegEx($data, $this->_regex['email']);
    }

    public function validateIpv4($data)
    {
        return $this->validateRegEx($data, $this->_regex['ipv4']);
    }

    public function validateSlug($data)
    {
        return $this->validateRegEx($data, $this->_regex['slug']);
    }

    public function validateText($data)
    {
        return $this->validateRegEx($data, $this->_regex['text']);
    }

    public function validateNotEmpty($data)
    {
        return !empty($data);
    }

    public function validateMaxLength($data, $length)
    {
        return (strlen((string) $data) <= $length);
    }

    public function validateMinLength($data, $length)
    {
        return (strlen((string) $data) >= $length);
    }

    public function validateRecordExists($data, $table, $field)
    {
        $adapter = \Library\Db\Adapter::getInstance();
        $row     = $adapter->fetchRow(
            $table,
            '`' . $field . '` = ' . $adapter->quote($data)
        );
        return ($row->columnCount() > 0);
    }

    public function validateRecordNotExists($data, $table, $field)
    {
        $adapter = \Library\Db\Adapter::getInstance();
        $row     = $adapter->fetchRow(
            $table,
            '`' . $field . '` = ' . $adapter->quote($data)
        );
        return !($row->columnCount() > 0);
    }

    public function validateIdentical($data, $second)
    {
        return ($data == $second);
    }

}