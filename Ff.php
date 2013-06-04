<?php
class Ff extends lib_Core
{
    public static function run()
    {       
        $config = self::getConfig();
        $route = parent::doRoute();
    }

    public static function getRequest()
    {
        return new lib_Request();
    }

    public static function getConfig($module = false)
    {
        return lib_Config::getConfig($module);
    }

    public static function getModel($model)
    {
        $model      = explode('/', $model);
        $module     = ucfirst($model[0]);
        $model_name = ucfirst($model[1]);

        $route = $module.'_Model_'.$model_name;
        return new $route;
    }

    public static function getResourseModel($module)
    {
        //$config = Ff::getConfig($module);

        if(isset($config['models']['resource_model']['calss']) && isset($config['models']['resource_model']['table']))
        {
            $class = $config['models']['resource_model']['calss'];
            $table = $config['models']['resource_model']['table'];

        
            if(class_exists($class))
            {
                return new $class($table);
            }
        }
        else
        {
            return null;
        }
    }
}
