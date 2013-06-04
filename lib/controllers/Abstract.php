<?php
class lib_controllers_Abstract /*extends lib_Core*/
{
    protected function getLayout()
    {
        $layout = new lib_Layout();
        
        
    }
    protected function renderLayout()
    {
        $layout = new lib_Layout();
        $layout->renderLayout();
        
        
    }

    
}
