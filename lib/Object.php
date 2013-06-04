<?php
class lib_Object
{
    public function __call($name, $arguments)
    {
        $accessor = substr($name, 0, 3);
        $param    = strtolower(substr($name, 3));
        
        if($accessor == 'get')
        {
            return $this->_params[$param];
        }
        elseif($accessor == 'set')
        {
            $this->_params[$param] = $arguments[0];
        }
        else
        {
            echo "Invalid Accessor Must be set or get";
        }

        //~ pri('Core_Request _call');
        //~ pri($name);
        //~ pri($arguments);
    }
}
