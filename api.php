<?php
  $arr = array(
    'path'         =>  $_SERVER['REQUEST_URI'],
    'ip_address'   =>  $_SERVER['REMOTE_ADDR'],
    'user_agent'   =>  $_SERVER['HTTP_USER_AGENT'],
    'timestamp'    =>  time()
  );

  echo json_encode($arr);
?>
