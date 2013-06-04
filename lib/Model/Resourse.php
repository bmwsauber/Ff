<?php
class lib_Model_Resourse extends lib_Db implements Iterator
{
    protected $_str;
    protected $_where_status = false;
    protected $_res;

    public function getData()
    {
        return $this->_query();
    }

    protected function _query()
    {
        return $this->query($this->_str);
    }

    public function load($id)
    {
        if(is_array($id)){
            $where_statment = "WHERE `main`.`{$this->_primary_key}` IN ('".implode("','", $id)."')";
        }
        else{
            $where_statment = "WHERE `main`.`{$this->_primary_key}` = '{$id}'";
        }

        $this->_str = "SELECT * FROM `{$this->_table}` AS main  ".$where_statment;

        return $this;
    }
    public function getCollection()
    {
        $this->_str = "SELECT * FROM `{$this->_table}` AS main ";

        return $this;
    }

    public function addFieldToFilter($key, $val, $statment = '=')
    {
        if(!$this->_where_status){
            $where = 'WHERE';
            $this->_where_status = true;
        }
        else{
            $where = 'AND';
        }
        $this->_str .= " {$where} `main`.`{$key}` {$statment} '{$val}'";
        return $this;
    }

    protected function _implodeItem(&$item, $key) // Note the &$item
    {
        $item = "`".$key."`"."="."'".$item."'";
    }

    public function save()
    {
        if($this->id)
        {  
            array_walk($this->data, array("lib_Model_Resourse", "_implodeItem"));
            $expr = implode(' , ', $this->data);
            $this->_str = "UPDATE `{$this->_table}` SET {$expr} WHERE `{$this->_primary_key}` = '{$this->id}'";
            return $this->_query();
        }
        else
        {
            if(is_array(reset($this->data))){
                $values_1 = array(); $values_2 = array();

                $data = $this->data;
                array_unshift($data, null);
                $values_1 = call_user_func_array("array_map", $data);

                foreach($values_1 as $arr_values){
                    $values_2[] = "('".implode("','", $arr_values)."')";
                }
                $values = implode(', ', $values_2);
            }
            else{
                $values = "('".implode("','", array_values($this->data))."')";
            }
            
            $this->_str = "INSERT INTO `{$this->_table}` (`".implode('`,`', array_keys($this->data))."`) VALUES ".$values;
            return $this->_query();
        }
    }
}


