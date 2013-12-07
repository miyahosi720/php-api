<?php
require('uri.php');
require('validate.php');
require('csv.php');

$request_uri = $_SERVER['REQUEST_URI'];
var_dump($request_uri);

$response = array();

try {
    //リクエストメソッドがGETかをチェック
    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        //GET以外のメソッド
        //405 Method Not Allowed
        throw new MethodNotAllowdException('GET以外のメソッドです');
    }

    //"/y-api/v1/SearchItems.json" のようなURIについて、SearchItemsをaction, jsonをformatと呼ぶ。
    //REQUEST_URIの形式チェックを行うと同時に、action, formatの抽出を行う
    $uri = new Uri();
    $requested = $uri->extractRequestedActionAndFormat($request_uri);

    if ($requested === false) {
        //urlが間違っている
        //404 Not Found
        throw new NotFoundException('urlの形式が不正です');
    }

    $action = $requested['action'];
    $format = $requested['format'];

    //APIの種類(action)ごとに処理を書く
    switch ($action) {

        case 'SearchItems':

            $req_params = $_GET;

            //GETパラメーターのバリデーション    
            $val = new validate();
            $params = $val->validateGetParams($req_params);

            if ($params === false) {
                //GETのパラメーターが間違っている
                //400 Bad Request
                throw new BadRequestException('GETのパラメーターが間違っています');
            }

            //CSVから商品を検索
            $csv = new Csv();
            //カテゴリID、価格範囲に合う商品データを取得
            $picked_items = $csv->pickUpRecordsByConditions($params['category_id'], $params['price_min'], $params['price_max']);

            //ソート
            $sorted_items = $csv->sort($picked_items, $params['sort']);

            //ページネーション
            $paginated_items = $csv->pagination($sorted_items, $params['count_per_page'], $params['page_number']);

            $item = $paginated_items;
        break;

        case 'LookUpItem':

echo 'LookUpItemだよ';

        break;

        default:
            //404 NOT FOUND
            throw new NotFoundException('そのようなAPIのアクションはありません');
        break;
    }

    //var_dump($item);
    if (!is_array($item)) {
        //返り値が配列ではない
        //500 Internal Server Error
        throw new InternalServerErrorException ('商品データの返り値が不正');
    }

    $item_count = count($item);

    $response['item'] = $item;
    $response['item_count'] = $item_count;
    $response['requested_url'] = 'http://' . $_SERVER['SERVER_NAME'] . $request_uri;
    $response['timestamp'] = time();

} catch (BadRequestException $e) {
    //400 Bad Request
    header("HTTP/1.1 400 Bad Request");
    $response['error'] = array(
        'code' => '400',
        'message' => 'Bad Request'
    );
} catch (NotFoundException $e) {
    //404 NOT FOUND
    header("HTTP/1.1 404 Not Found");
    $response['error'] = array(
            'code' => '404',
            'message' => 'The URL You Requested Was Not Found'
        );
} catch (MethodNotAllowdException $e) {
    //405 Method Not Allowed
    header("HTTP/1.1 405 Method Not Allowed");
    $response['error'] = array(
            'code' => '405',
            'message' => 'Method Not Allowed'
        );
} catch (InternalServerErrorException $e) {
    // 500 Internal Server Error
    header("HTTP/1.1 500 Internal Server Error");
    $response['error'] = array(
            'code' => '500',
            'message' => 'Server Error'
        );
}

class NotFoundException extends Exception {}

class MethodNotAllowdException extends Exception {}

class BadRequestException extends Exception {}

class InternalServerErrorException extends Exception {}

//var_dump($response);
//var_dump($format);

if ($format == 'xml') {
    header("Content-Type: text/xml; charset=utf-8");
    //xml形式で出力
    echo 'aaa';
} else {
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($response);
}


?>