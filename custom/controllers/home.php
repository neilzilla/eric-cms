<?php
  $site = $_SERVER['HTTP_HOST'];
  $ab = strpos($site, 'adambartlettdesign.co.uk') !== false ? true : false;
  $return = array('ab' => $ab);
?>