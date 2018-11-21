<?php 
  foreach($page->children() as $section){
    $s['title'] = (string)$section->title();
    $s['subtitle'] = (string)$section->subtitle();
    $s['background']['backgroundColor'] = (string)$section->colour();
    $s['image'] = (string)$section->image();
    $s['name'] = (string)$section->slug();
    $s['icon'] = (string)$section->icon();
    $json['sections'][] = $s;
  }
  echo json_encode($json);    
?>