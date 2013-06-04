<?php
class lib_Request extends lib_Object
{
    protected $_params = array();

    public function __construct()
    {
        $this->_params = $this->_parceParams();
    }


    public function getParam($argument)
    {
        return (isset($this->_params[$argument])) ? $this->_params[$argument] : null;
    }

   

    protected function _parceParams()
    {
        if(isset($_REQUEST['params']) && $_REQUEST['params'])
        {
            return $_REQUEST['params'];
        }
        else
        {
            $query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);

            parse_str($query, $params);

            return $params;
            
            
        }
    }


}
