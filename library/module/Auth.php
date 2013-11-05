<?php

namespace Library\Module;

class Auth extends \Library\Module{

    const STORAGE_COOKIES = 0;
    const STORAGE_SESSION = 1;

    private $_table;
    private $_id_col;
    private $_password_col;
    private $_salt_col = null;
    private $_hashing = 'md5';
    private $_storage = self::STORAGE_COOKIES;
    private $_remember_me = false;
    private $_remember_time = 2592000;
    private $_user_row = null;
    private $_data = false;

    public function setTable($table)
    {
        $this->_table = strval($table);
        return $this;
    }

    public function setIdCol($col)
    {
        $this->_id_col = $col;
        return $this;
    }

    public function setPasswordCol($col)
    {
        $this->_password_col = $col;
        return $this;
    }

    public function setSaltCol($col)
    {
        $this->_salt_col = $col;
        return $this;
    }

    public function setHashing($method)
    {
        $this->_hashing = $method;
        return $this;
    }

    public function setStorage($storage)
    {
        if(
            in_array(
                $storage,
                array(
                    self::STORAGE_COOKIES,
                    self::STORAGE_SESSION
                )
            )
        ){
            $this->_storage = $storage;
        }
        return $this;
    }

    public function rememberMe()
    {
        $this->_remember_me = true;
        return $this;
    }

    public function setRememberTime($time)
    {
        $this->_remember_time = intval($time);
        return $this;
    }

    public function getHash($clean_password, $salt)
    {
        $callback = $this->_hashing;
        return $callback($clean_password . $salt);
    }

    public function setData($id, $password)
    {
        $this->_data = array(
            'id'       => $id,
            'password' => $password
        );
        /*
        if($this->_storage == self::STORAGE_SESSION){
            $_SESSION['auth']['id']       = $id;
            $_SESSION['auth']['password'] = $password;
        } else {
            if($this->_remember_me){
                $expire = time() + $this->_remember_time;
            } else {
                $expire = 0;
            }
            setcookie('id', $id, $expire);
            setcookie('password', $password, $expire);
        }*/
        return $this;
    }

    public function signIn()
    {
        if(!$this->_data){
            if ($this->_storage == self::STORAGE_SESSION) {
                $id       = $_SESSION['auth']['id'];
            } else {
                $id       = $_COOKIE['id'];
            }
        } else {
            $id = $this->_data['id'];
        }
        $adapter = \Library\Db\Adapter::getInstance();
        $row = $adapter->fetchRow($this->_table, '`' . $this->_id_col . '` = ' . $adapter->quote($id));
        $array = $row->fetch(\PDO::FETCH_ASSOC);
        if($array){
            if (!$this->_data) {
                if ($this->_storage == self::STORAGE_SESSION) {
                    $password = $_SESSION['auth']['password'];
                } else {
                    $password = $_COOKIE['password'];
                }
            } else {
                $password = $this->_data['password'];
                $password = $this->getHash($password, $array[$this->_salt_col]);
            }
            if($array[$this->_password_col] == $password){
                $this->_user_row = $array;
                \Library\Registry::getInstance()->log->writeSuccess('Logged in as "' . $id . '"');
                if ($this->_storage == self::STORAGE_SESSION) {
                    $_SESSION['auth']['id']       = $id;
                    $_SESSION['auth']['password'] = $password;
                } else {
                    if ($this->_remember_me) {
                        $expire = time() + $this->_remember_time;
                    } else {
                        $expire = 0;
                    }
                    setcookie('id', $id, $expire);
                    setcookie('password', $password, $expire);
                }
                return true;
            }
        }
        \Library\Registry::getInstance()->log->writeWarning('Failed to log in!');
        return false;
    }

    public function check()
    {
        if($this->_user_row){
            return true;
        }
        return false;
    }

    public function signOut()
    {
        $_SESSION['auth'] = false;
        setcookie('login', false);
        setcookie('password', false);
        return $this;
    }

    public function getUser()
    {
        return $this->_user_row;
    }

}