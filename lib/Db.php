<?php
class lib_Db
{
    protected $_dbh;

    public function __construct()
    {
        $config = Ff::getConfig('default/db');
        $this->_dbh = new PDO("mysql:host={$config['db_host']};dbname={$config['db_name']}", $config['db_user'], $config['db_pass'], array(PDO::ATTR_PERSISTENT => true));
    }

    public function query($query)
    {
        var_dump($query);
        exit;
        return $this->_dbh->query($query);
    }

    public function rewind()
    {
        $this->_res = $this->query($this->_str)->fetchAll(PDO::FETCH_OBJ);
        reset($this->_res);
    }
  
    public function current()
    {
        $var = current($this->_res);
        return $var;
    }
  
    public function key() 
    {
        $var = key($this->_res);
        return $var;
    }
  
    public function next() 
    {
        $var = next($this->_res);
        return $var;
    }
  
    public function valid()
    {
        $key = key($this->_res);
        $var = ($key !== NULL && $key !== FALSE);
        return $var;
    }
    


}
