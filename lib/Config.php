<?php
class lib_Config
{
    const XML_NAMESPACE = 'http://framework.zend.com/xml/zend-config-xml/1.0/';

    public static function getConfig($section = false)
    {
        $path = PATH_ROOT.DS.'etc';
        $directory = new RecursiveDirectoryIterator($path);
        $files = new RecursiveIteratorIterator($directory);
        $config = array();

        foreach($files as $file)
        {
            $xml = simplexml_load_file($files->getPathname());
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

    protected static function _arrayMergeRecursive($firstArray, $secondArray)
    {
        if (is_array($firstArray) && is_array($secondArray)) {
            foreach ($secondArray as $key => $value) {
                if (isset($firstArray[$key])) {
                    $firstArray[$key] = self::_arrayMergeRecursive($firstArray[$key], $value);
                } else {
                    if($key === 0) {
                        $firstArray= array(0=>self::_arrayMergeRecursive($firstArray, $value));
                    } else {
                        $firstArray[$key] = $value;
                    }
                }
            }
        } else {
            $firstArray = $secondArray;
        }
        return $firstArray;
    }

    protected static function _toArray(SimpleXMLElement $xmlObject)
    {
        $config       = array();
        $nsAttributes = $xmlObject->attributes(self::XML_NAMESPACE);

        // Search for parent node values
        if (count($xmlObject->attributes()) > 0) {
            foreach ($xmlObject->attributes() as $key => $value) {
                if ($key === 'extends') {
                    continue;
                }

                $value = (string) $value;

                if (array_key_exists($key, $config)) {
                    if (!is_array($config[$key])) {
                        $config[$key] = array($config[$key]);
                    }

                    $config[$key][] = $value;
                } else {
                    $config[$key] = $value;
                }
            }
        }

        // Search for local 'const' nodes and replace them
        if (count($xmlObject->children(self::XML_NAMESPACE)) > 0) {
            if (count($xmlObject->children()) > 0) {
                throw new Exception("A node with a 'const' childnode may not have any other children");
            }

            $dom                 = dom_import_simplexml($xmlObject);
            $namespaceChildNodes = array();

            // We have to store them in an array, as replacing nodes will
            // confuse the DOMNodeList later
            foreach ($dom->childNodes as $node) {
                if ($node instanceof DOMElement && $node->namespaceURI === self::XML_NAMESPACE) {
                    $namespaceChildNodes[] = $node;
                }
            }

            foreach ($namespaceChildNodes as $node) {
                switch ($node->localName) {
                    case 'const':
                        if (!$node->hasAttributeNS(self::XML_NAMESPACE, 'name')) {
                            throw new Exception("Misssing 'name' attribute in 'const' node");
                        }
                        $constantName = $node->getAttributeNS(self::XML_NAMESPACE, 'name');

                        if (!defined($constantName)) {
                            throw new Exception("Constant with name '$constantName' was not defined");
                        }
                        $constantValue = constant($constantName);
                        $dom->replaceChild($dom->ownerDocument->createTextNode($constantValue), $node);
                        break;

                    default:
                        throw new Exception("Unknown node with name '$node->localName' found");
                }
            }

            return (string) simplexml_import_dom($dom);
        }

        // Search for children
        if (count($xmlObject->children()) > 0) {
            foreach ($xmlObject->children() as $key => $value) {
                if (count($value->children()) > 0 || count($value->children(self::XML_NAMESPACE)) > 0) {
                    $value = self::_toArray($value);
                } else if (count($value->attributes()) > 0) {
                    $attributes = $value->attributes();
                    if (isset($attributes['value'])) {
                        $value = (string) $attributes['value'];
                    } else {
                        $value = $this->_toArray($value);
                    }
                } else {
                    $value = (string) $value;
                }

                if (array_key_exists($key, $config)) {
                    if (!is_array($config[$key]) || !array_key_exists(0, $config[$key])) {
                        $config[$key] = array($config[$key]);
                    }

                    $config[$key][] = $value;
                } else {
                    $config[$key] = $value;
                }
            }
        } else if (!isset($xmlObject['extends']) && !isset($nsAttributes['extends']) && (count($config) === 0)) {
            // Object has no children nor attributes and doesn't use the extends
            // attribute: it's a string
            $config = (string) $xmlObject;
        }

        return $config;
    }
}
