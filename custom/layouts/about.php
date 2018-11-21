<?php 


  $json['contentTitle'] = (string)$page->contentTitle();
  $json['hiFiText'] = (string)$page->hiFiText();
  $json['loFiText'] = (string)$page->loFiText();
  $json['hiFiImage'] = (string)$page->hiFiImage();
  $json['loFiImage'] = (string)$page->loFiImage();
  
  echo json_encode($json);
?>