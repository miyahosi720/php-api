<?php
//エラー表示
error_reporting(E_ALL & ~E_NOTICE);

//var_dump($_SERVER['PATH_INFO']);
//var_dump($_SERVER['REQUEST_URI']);
//var_dump($_GET);

require('ApiServer.php');
require('exceptions.php');

$api_server = new ApiServer();

try {
    //リクエストメソッドがGETかをチェック
    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        //405 Method Not Allowed
        throw new MethodNotAllowdException('HTTP method is not allowed');
    }

    switch ($_SERVER['PATH_INFO']) {

        case '/items':
            $items = $api_server->getItemsList($_GET);

            if ($items === false) {
                //GETパラメーターエラー
                throw new BadRequestException('Keyword parameter is not valid');
            }

            if ($_GET['format'] == 'xml') {
                //正常レスポンス(xml)を生成
                $response ="<result><hoge>hello</hoge></result>";

            } else {
                //正常レスポンス(json)を生成
                $response_array['result'] = array(
                    'requested' => array(
                            'parameter' => $_GET,
                            'url' => 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'],
                            'timestamp' => time()
                        ),
                    'items_count' => count($items),
                    'items' => $items
                    );

                $response = json_encode($response_array);
            }

        break;

        case '/item':
            $response = $api_server->getItemDetail($_GET);

            //正常レスポンス(json)を生成

            //正常レスポンス(xml)を生成

        break;

        default:
            //404 NOT FOUND
            throw new NotFoundException('The URL you requested was not found');
        break;
    }

} catch (BadRequestException $e) {
    //400 Bad Request
    header("HTTP/1.1 400 Bad Request");
    $response_array['error'] = array(
        'code' => '400',
        'message' => $e->getMessage()
    );
} catch (NotFoundException $e) {
    //404 NOT FOUND
    header("HTTP/1.1 404 Not Found");
    $response_array['error'] = array(
            'code' => '404',
            'message' => $e->getMessage()
        );
} catch (MethodNotAllowdException $e) {
    //405 Method Not Allowed
    header("HTTP/1.1 405 Method Not Allowed");
    $response_array['error'] = array(
            'code' => '405',
            'message' => $e->getMessage()
        );
} catch (Exception $e) {
    // 500 Internal Server Error
    header("HTTP/1.1 500 Internal Server Error");
    $response_array['error'] = array(
            'code' => '500',
            'message' => 'Server Error'
        );
}

if (isset($response_array['error'])) {
    if ($_GET['format'] == 'xml') {
        //エラーレスポンスxml
        $response ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><error><code>". $response_array['error']['code'] ."</code><message>" . $response_array['error']['message'] . "</message></error>";
    } else {
        //エラーレスポンスjson
        $response = json_encode($response_array);
    }

}

if ($_GET['format'] == 'xml') {
    //xmlヘッダセット
    header("Content-Type: text/xml; charset=utf-8");

} else {
    //jsonヘッダセット
    header("Content-Type: application/json; charset=utf-8");
}

echo $response;


