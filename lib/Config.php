<?php
class lib_Config extends lib_Core
{
    public static function getConfig($section = false)
    {
        $config = array();

        /******** config.xml ******************/
        $xml = simplexml_load_file(PATH_ROOT.DS.'etc'.DS.'config.xml');
        $array = self::_toArray($xml);
        $config = self::_arrayMergeRecursive($config, $array);
        /*************************************/

        foreach (glob(PATH_ROOT.DS.'etc'.DS.'modules'.DS.'mod*.xml') as $filename) {
            $xml = simplexml_load_file($filename);
            $array = self::_toArray($xml);
            $config = self::_arrayMergeRecursive($config, $array);
        }

        if($section)
        {
            $default = null;
            // accept a/b/c as ['a']['b']['c']
            if (strpos($section,'/')) {
                $keyArr = explode('/', $section);

                $data = $config;
                foreach ($keyArr as $i=>$k) {
                    if ($k==='') {
                        return $default;
                    }
                    if (is_array($data)) {
                        if (!isset($data[$k])) {
                            return $default;
                        }
                        $data = $data[$k];
                    } else {
                        return $default;
                    }
                }
                return $data;
            }
            return $config[$sections];
        }
        return $config;
    }
    
}
