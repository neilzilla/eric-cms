<?php
class Template {
  
    var $page;
    public $controller;
    
    function __construct($page){
        $this->page = $page;
        $this->controller = function($page){
          $return = null;
          if(file_exists(ROOT . '/custom/controllers/' . $page->template() . '.php')) include(ROOT . '/custom/controllers/' . $page->template() . '.php');        
          return $return;
        };
      
        if(isset($controller)) $this->controller = $controller;
        if(!file_exists(ROOT . '/custom/layouts/' . $page->template() . '.php')) return false;  // Template doesn't exist
        return true;
    }
  
    function exec($static = false){
        $vars = ($this->controller)($this->page);
        $vars['page'] = $this->page;
        if($static) ob_start();
        $output = function($reservedVars){
            if(is_array($reservedVars)) foreach($reservedVars as $k=>$v) ${$k} = $v;
            unset($reservedVars);
            if(file_exists(ROOT . '/custom/layouts/' .$page->template() . '.php')) {
                include(ROOT . '/custom/layouts/' .$page->template() . '.php');
            }else{
              echo 'No layout for ' . $page->template();
            }
          };
        $output($vars);
      /*
        if($static){
            $staticPage = ob_get_flush();
            file_put_contents(ROOT . '/tmp/home.html', $staticPage);
          ob_end_clean();
        }*/
    }
 
    
}
?>