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
    public static function getRoute()
    {
        $module     = strtolower($_REQUEST['module']);
        $controller = strtolower($_REQUEST['controller']);
        $action     = strtolower($_REQUEST['action']);

        $route = $module.'_'.$controller.'_'.$action;
        return $route;
    }

    public static function getModule()
    {
        return $module = strtolower($_REQUEST['module']);
    }

    const XML_NAMESPACE = 'http://framework.zend.com/xml/zend-config-xml/1.0/';
    
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
                        $value = self::_toArray($value);
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
}
