<?php

  header("Content-Type: application/json; charset=utf-8");

  $response = array();

  switch ($_SERVER['REQUEST_URI']) {
    case '/y-api/hello':
      $response['data'] = 'world';
      break;
    case '/y-api/item':
      $response['data'] = array(
        'id'          => 100,
        'title'       => 'やばい商品',
        'price'       => 5000,
        'description' => 'これはまじでやばい'
      );
      break;

    case '/y-api/items':
      $response['item'];

      break;

    default:
        //エラーとエラーコード、エラーメッセージ（リクエストは成功している）

      break;
  }

  var_dump($response);
  echo json_encode($response);
?>
