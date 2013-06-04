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
       
        $classFile = str_replace(' ', DS, ucwords(str_replace('_', ' ', $class)));
        $classFile.= '.php';
        
        if(file_exists($classFile) )
        {
            return include $classFile;
        }
        else
        {  
            echo file_get_contents(PATH_ROOT.DS.'errors'.DS.'404.phtml');
            exit;
        }

        
    }
}

