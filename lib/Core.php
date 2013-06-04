<?php
class lib_Core
{
    
     
    public static function doRoute()
    {
        $module     = ucfirst($_REQUEST['module']);
        $controller = ucfirst($_REQUEST['controller']);
        $action     = strtolower($_REQUEST['action']).'Action';

        $route = $module.'_controllers_'.$controller.'Controller';

        $class = new $route;
        $class->$action();
    }

    public static function getModule()
    {
        return $module = strtolower($_REQUEST['module']);
    }
    
}
