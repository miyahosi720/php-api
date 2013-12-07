<?php
require('uri.php');
require('validate.php');
require('csv.php');
require('exceptions.php');

$request_uri = $_SERVER['REQUEST_URI'];

$response = array();

try {
    //リクエストメソッドがGETかをチェック
    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        //GET以外のメソッド
        //405 Method Not Allowed
        throw new MethodNotAllowdException('GET以外のメソッドです');
    }

    $req_params = $_GET;

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

            //GETパラメーターのバリデーション    
            $val = new validate();
            $params = $val->validateSearchItemsParams($req_params);

            if ($params === false) {
                //GETのパラメーターが間違っている
                //400 Bad Request
                throw new BadRequestException('GETのパラメーターが間違っています');
            }

            $csv = new Csv();
            //カテゴリID、価格範囲に合う商品データをCSVから取得
            $picked_items = $csv->pickUpRecordsByConditions($params['category_id'], $params['price_min'], $params['price_max']);

            //ソート
            $sorted_items = $csv->sort($picked_items, $params['sort']);

            //ページネーション
            $paginated_items = $csv->pagination($sorted_items, $params['count_per_page'], $params['page_number']);

            $item = $paginated_items;
        break;

        case 'LookUpItem':

            //GETパラメーターのバリデーション 
            $val = new validate();
            $params = $val->validateLookUpItemParam($req_params);

            if ($params === false) {
                //GETのパラメーターが間違っている
                //400 Bad Request
                throw new BadRequestException('GETのパラメーターが間違っています');
            }

            $csv = new Csv();
            //指定されたproduct_idに合う商品データをCSVから取得
            $picked_item = $csv->pickUpRecordById($params['product_id']);

            $item = $picked_item;
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

//レスポンス出力
if ($format == 'xml') {
    header("Content-Type: text/xml; charset=utf-8");
    //ToDo: xml形式で出力する処理を書く

} else {
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($response);
}


?>