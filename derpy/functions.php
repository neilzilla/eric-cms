<?php

    function go($url){
        header('Location: ' . SITE_URL . $url);
    }

    function validSlug($slug){
      return preg_match('#^[a-zA-Z0-9-/?=%&_]*$#', $slug);
    }
    function strrtrim($message, $strip) { 
        // break message apart by strip string 
        $lines = explode($strip, $message); 
        $last  = ''; 
        // pop off empty strings at the end 
        do { 
            $last = array_pop($lines); 
        } while (empty($last) && (count($lines))); 
        // re-assemble what remains 
        return implode($strip, array_merge($lines, array($last))); 
    } 
  

?>