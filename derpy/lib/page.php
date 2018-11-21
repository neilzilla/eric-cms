<?php

    function Page($page){
      return new Page($page);
    }

    function resolve_path($path){
      if(!$path) return '';
      $path = explode('/', $path);
      if($path[0] === '') $path = array_slice($path, 1);
      if($path[count($path)-1] === '') $path = array_slice($path, 0, count($path)-1);
      $rebuilt = '';
      $match = false;
      foreach($path as $folder){
        $match = false;
        foreach(scandir(ROOT . '/content/' . $rebuilt) as $dir){
          if(preg_match('/^[0-9-]*' . $folder . '$/', $dir)){
            $rebuilt .= $dir . '/';
            $match = true;
            break;
           }
        }
        if($match != true) break;
      }
      return $match ? rtrim($rebuilt, '/') : false;
    }

    class Page {
      
      var $slug = array('type' => 'text', 'val' => '');
      var $path = array('type' => 'text', 'val' => '');
      var $resolved = array('type' => 'text', 'val' => '');
      var $template = array('type' => 'text', 'val' => '');
      
      function __call($name, $args){
        $var = $this->{$name};
        $class = 'Derp' . ucwords($var['type']);
        $prop = new $class($var['val']);
        return $prop;        
      }
      
      function __construct($path = false){
          if($path) $this->init($path);
      }
      
      function init ($path){
               
        // Clean
        $path = strtolower($path);
        $breadcrumbs = explode('?', $path);
        $path = trim($breadcrumbs[0], '/');
        $breadcrumbs = explode('/', $path);
        $slug = explode('-',end($breadcrumbs));
        if(preg_match('/^[0-9]+$/', $slug[0])) unset($slug[0]);
        $this->slug['val'] = implode('-', $slug);
        
        $resolved = resolve_path($path);
        
        if($resolved === false) return array('err' => 'path ' . $path . ' doesn\'t exist');

        if(!file_exists(ROOT . '/content/' . $resolved)) return array('err' => 'no dir');                                          // Error no dir exists
        $pageName = end($breadcrumbs);
        
        $this->path['val'] = $path;
        $this->resolved['val'] = $resolved;
        
        if(!$pageName){
            $path = '';
        }
        
        $files = scandir(ROOT . '/content/' . $resolved);
        foreach($files as $file){
            if(preg_match('/^[a-z0-9]*.yaml$/', $file)){
                $template = $file;
                break;
            }
        }
        
        if(!isset($template)) return array('err' =>'no template');                             // No template
        $this->template['val'] = strrtrim( $template, '.yaml');
        $content = file_get_contents(ROOT .'/content/' . $resolved . '/' . $template);
        $content = strpos($content, ':') ? yaml_parse($content) : false;
        if(!$content) return array('err' => 'no content');                         // Error no page contents

        $schema = file_exists(ROOT .'/custom/schema/' . $template) ? yaml_parse_file(ROOT .'/custom/schema/' . $template) : '';
        if(!$schema) return array( 'err' => 'schema not found for ' . $this->template['val']);                         // Error no page contents
        
        if(!isset($schema['data'])) return array('err' => 'Invalid or incomplete schema');
        
        foreach($schema['data'] as $k=>$v){
            $this->{$k} = array('type' => $v['type'], 'val' => '');
        }
        
        foreach($content as $k=>$v){
            if(isset($this->{$k})) $this->{$k}['val'] = $v;
        }
        
        return true;
      }
      
      function children() {
          $children = new Pages((string)$this->path());

        return $children;
      }
      
      function images() {
          return new Files($this->path['val']);
      }
      
    }

    class Pages implements Iterator {
      var $content = array();
      

      function __construct($layer = false){
          $this->initLayer($layer);
      }
        
      function initLayer($layer=false){
          $layer = resolve_path($layer);
          $items = array();
          if($layer) $layer = trim($layer, '/') . '/';
          foreach(scandir(ROOT . '/content/' . $layer) as $item){
            if(($item !== ('.' | '..')) && validSlug($item)) $items[] = new Page($layer . $item);
          };


          $this->content = $items;
          return;
      }
      
      function sortBy($key, $dir = 'asc'){
          if(!count($this->content)) return $this;
          foreach($this->content as $k=>$v) {
              $sort[$k] = $v->{$key}['val'];
          }
          ($dir == 'desc') ? arsort($sort) : asort($sort);
          foreach($sort as $k=>$v) $sorted[] = $this->content[$k];
          $this->content = $sorted;
          return $this;
      }
      
    public function rewind()
    {
        reset($this->content);
    }
  
    public function current()
    {
        $var = current($this->content);
        return $var;
    }
  
    public function key() 
    {
        $var = key($this->content);
        return $var;
    }
  
    public function next() 
    {
        $var = next($this->content);
        return $var;
    }
  
    public function valid()
    {
        $key = key($this->content);
        $var = ($key !== NULL && $key !== FALSE);
        return $var;
    }


    }

    class DerpText {
        public $val = false;
        function __construct($val){
            $this->val = $val;
        }
      
        function __toString(){
            return $this->val;
        }
      
        function toUpper(){
           return new derpText(strtoupper($this->val));
        }
      
        function toLower(){
           return new derpText(strtolower($this->val));
        }
    }

   class derpTextarea extends DerpText {
   }

    class DerpArray {
        public $val = false;
        function __construct($val){
            $this->val = $val;
        }
      
        function __toString(){
            return json_encode($this->val);
        }
    }
      
  class File {
      var $parent = array('type' => 'text', 'val' => '');
      var $filename = array('type' => 'text', 'val' => '');
      
      function __construct($filepath = false){
          if($filepath) $this->init($filepath);
      }
    
      function __call($name, $args){
        $var = $this->{$name};
        $class = 'Derp' . ucwords($var['type']);
        $prop = new $class($var['val']);
        return $prop;        
      }
    
      function init($path){
          if(!file_exists(ROOT . '/content/' . $path)) return false;
          $parts = explode('/', $path);
          $this->filename['val'] = $parts[count($parts)-1];
          unset($parts[count($parts)-1]);
          $this->parent['val'] = implode('/', $parts);
      }
    
      function url(){
        return SITE_URL . '/content/' . $this->parent['val'] . '/' . $this->filename['val'];
      }
  }

  class Files implements Iterator {
    var $files = array();
    
    function __construct($parent){
        if($parent) $this->init($parent);
    }
      
    function init($parent) {
      $files = scandir(ROOT . '/content/' . $parent);
      foreach($files as $file){
        if(preg_match('/^[a-z0-9_]*.jpg$/', $file)){
            $this->files[] = new File($parent . '/' . $file);
        }
      }
      
     }
    
    function first(){
      return $this->files[0];
    }
          
    public function rewind()
    {
        reset($this->files);
    }
  
    public function current()
    {
        $var = current($this->files);
        return $var;
    }
  
    public function key() 
    {
        $var = key($this->files);
        return $var;
    }
  
    public function next() 
    {
        $var = next($this->files);
        return $var;
    }
  
    public function valid()
    {
        $key = key($this->files);
        $var = ($key !== NULL && $key !== FALSE);
        return $var;
    }
    
  }

  class derpy {
    private $routes = array();
    
    function add_routes($routes){
      if(!is_array($routes)) return false;
      if($routes['path'] && $routes['action']){
        $routes = array($routes);
      }
      foreach($routes as $route){
        if(!$route['path'] || !$route['action']) return 'no vars';
        $this->routes[] = $route;
      }
      return true;
    }
    
    function do_routes($path){
      $path = trim($path, '/');
      $path = explode('/', $path);
      $return = true;
      foreach($this->routes as $route){
         $check = $this->check_route($path, $route);
         if($check){
            $return = ($check['action'])($check['args']);
            break;
         }
      }
      if($return !== NULL){
        if($return === false) exit;
        //do data output check
      }
      
      return $return;
    
    }
    
    function check_route($path, $route){
        $r_path = trim($route['path'], '/');
        $r_path = explode('/', $r_path);

        if(count($path) !== count($r_path)) return;
        $route_path = array();
        foreach($r_path as $r){
          $r = str_replace('(:all)', '[a-zA-Z0-9_-]+', $r);
          $r = str_replace('(:numbers)', '[0-9]+', $r);
          $r = str_replace('(:letters)', '[a-zA-Z]+', $r);
          $route_path[] = '/^' . $r . '$/';
        }
        $matched = array();
        for($i = 0; $i < count($route_path); $i++){
          if(!preg_match($route_path[$i], $path[$i])) return false;
          $matched[] = $path[$i];
        }
      return array('args' => $matched, 'action' => $route['action']);
      
    }
  }
?>