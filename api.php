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
            $response = $api_server->searchItems($_GET);

        break;

        case '/item':
            $response = $api_server->getItemDetail($_GET);

            /*
            if ($item === false) {
                //GETパラメーターエラー
                throw new BadRequestException('Keyword parameter is not valid');
            }

            $item_count = empty($item) ? 0 : 1;

            if ($_GET['format'] == 'xml') {
                //正常レスポンス(xml)を生成
                $response ="<result><piyo>howdy</piyo></result>";

            } else {
                //正常レスポンス(json)を生成
                $response_array['result'] = array(
                    'requested' => array(
                            'parameter' => $_GET,
                            'url' => 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'],
                            'timestamp' => time()
                        ),
                    'item_count' => $item_count,
                    'item' => $item
                    );
                $response = json_encode($response_array);
            }
            */

        break;

        default:
            //404 NOT FOUND
            throw new NotFoundException('The URL you requested was not found');
        break;
    }

} catch (Exception $e) {

    // 500 Internal Server Error
    header("HTTP/1.1 500 Internal Server Error");

    if ($_GET['format'] == 'xml') {
        //500エラーレスポンス(xml)
        header("Content-Type: text/xml; charset=utf-8");
        $response ="<?xml version=\"1.0\" encoding=\"UTF-8\"?><error><code>500</code><message>Server Error</message></error>";
    } else {
        //500エラーレスポンス(json) 
        header("Content-Type: application/json; charset=utf-8");
        $response_array['error'] = array(
            'code' => '500',
            'message' => 'Server Error'
        );
        $response = json_encode($response_array);
    }
}

echo $response;


