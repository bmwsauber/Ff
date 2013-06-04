<?php
class lib_Layout extends lib_Core
{
    protected $roure;
    protected $default      = array();
    protected $route_layout = array();
    protected $layout       = array();

    
    public function __construct()
    {
        $layout = array();
        $this->route = $this->getRoute();

        foreach (glob(PATH_ROOT.DS.'design'.DS.'layout'.DS.'*.xml') as $filename) {
            $xml = simplexml_load_file($filename);
            $array = parent::_toArray($xml);
            $layout = parent::_arrayMergeRecursive($layout, $array);
        }

        $this->default          = $layout['default'];
        $this->route_layout     = $layout[$this->route];

        //$this->layout = parent::_arrayMergeRecursive($this->default, $this->route_layout);

        

        //~ pri($layout[$this->route]);
        //~ pri($layout['default']);


        return $this;
    }

    public function renderLayout()
    {

        pri($this->default);
        pri($this->route_layout);
    }

}
