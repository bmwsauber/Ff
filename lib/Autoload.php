<?php
class Lib_Autoload
{
    static protected $_instance;
    
    public static function register()
    {
        spl_autoload_register(array(self::instance(), 'autoload'));
    }

    static public function instance()
    {
        if (!self::$_instance) {
            self::$_instance = new Lib_Autoload();
        }
        return self::$_instance;
    }

    public function autoload($class)
    {
       
        $classFile = str_replace(' ', DIRECTORY_SEPARATOR, ucwords(str_replace('_', ' ', $class)));
        $classFile.= '.php';
        
        return include $classFile;
    }
}

