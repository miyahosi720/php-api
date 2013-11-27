<?php

  header("Content-Type: application/json; charset=utf-8");

  $response = array();

  switch ($_SERVER['REQUEST_URI']) {
    case '/api/hello':
      $response['data'] = 'world';
      break;
    case '/api/item':
      $response['data'] = array(
        'id'          => 100,
        'title'       => 'やばい商品',
        'price'       => 5000,
        'description' => 'これはまじでやばい'
      );
      break;
  }

  echo json_encode($response);
?>
