<?php
  header("Content-Type: application/json; charset=utf-8");

  $arr = array(
    'path'         =>  $_SERVER['REQUEST_URI'],
    'ip_address'   =>  $_SERVER['REMOTE_ADDR'],
    'user_agent'   =>  $_SERVER['HTTP_USER_AGENT'],
    'parent'       =>  array(
      'japanese' => 'リラックマ',
      'array'    => array('element1', 'element2', 'element3')
    ),
    'timestamp'    =>  time()
  );

  var_dump($arr);

  echo json_encode($arr);
?>
