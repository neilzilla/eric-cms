<?php
    $https = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? 'https://' : 'http://';
    define('SITE_URL', rtrim($https . $_SERVER['HTTP_HOST'] . pathinfo($_SERVER['PHP_SELF'])['dirname'], '/'));
    define('ROOT', rtrim($_SERVER['DOCUMENT_ROOT'] . pathinfo($_SERVER['PHP_SELF'])['dirname'], '/'));    

    $path = trim(str_replace(trim(pathinfo($_SERVER['PHP_SELF'])['dirname'], '/'), '', trim($_SERVER['REQUEST_URI'], '/')), '/');
    
    require_once(ROOT. '/derpy/functions.php');
    require_once(ROOT . '/derpy/lib/manifest.php');

    if(!validSlug($path)) go('');

    $derpy = new derpy();
    
    // Load Config
    if(file_exists(ROOT . '/custom/config/routes.php')) require_once(ROOT . '/custom/config/routes.php');
  
    $derpy->do_routes($path);
    
    $page = new Page();
    $setup = $page->init($path);

    if(isset($setup['err'])){
      echo $setup['err'];
      exit;
    }


    
    $output = new Template($page);
    
    $output->exec();
  

?>