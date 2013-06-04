<?php
class lib_Model extends lib_Model_Resourse
{
    protected $id;
    protected $data;

    public function setData($data_arr)
    {
        $this->data = $data_arr;


        //~ foreach($data_arr as $key => $val)
        //~ {
            //~ $this->$key = $val;
        //~ }
    }

    public function getData()
    {
        return $this->data;
    }


    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }
    
}
