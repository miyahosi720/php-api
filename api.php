<?php
  header("Content-Type: application/json; charset=utf-8");

  $arr = array(
    'path'         =>  $_SERVER['REQUEST_URI'],
    'ip_address'   =>  $_SERVER['REMOTE_ADDR'],
    'user_agent'   =>  $_SERVER['HTTP_USER_AGENT'],
    'japanese'     =>  'リラックマ',
    'timestamp'    =>  time()
  );

  echo json_encode($arr);
?>
