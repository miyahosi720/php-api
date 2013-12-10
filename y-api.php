<?php
//エラー表示
error_reporting(E_ALL & ~E_NOTICE);

require('uri.php');
require('csv.php');
require('exceptions.php');

$request_uri = $_SERVER['REQUEST_URI'];

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

    $csv = new Csv();

    $response = array();

    //APIの種類(action)ごとに処理を書く
    switch ($action) {

        case 'SearchItems':
            //GETパラメーターのバリデーション    
            $params = $uri->validateSearchItemsParams($req_params);

            if ($params === false) {
                //GETのパラメーターが間違っている
                //400 Bad Request
                throw new BadRequestException('GETのパラメーターが間違っています');
            }

            //カテゴリID、価格範囲に合う商品データをCSVから取得
            $picked_items = $csv->pickUpRecordsByConditions($params['category_id'], $params['price_min'], $params['price_max']);

            //ソート
            $sorted_items = $csv->sort($picked_items, $params['sort']);

            //ページネーション
            $paginated_items = $csv->pagination($sorted_items, $params['count_per_page'], $params['page_number']);

            $items = $paginated_items;

            if (!is_array($items)) {
                //返り値が配列ではない
                //500 Internal Server Error
                throw new Exception ('商品データの返り値が不正');
            }

            //出力するレスポンス内容をセット
            $response['result'] = array(
                'requested' => array(
                        'action' => $action,
                        'format' => $format,
                        'parameter' => $req_params,
                        'url' => 'http://' . $_SERVER['SERVER_NAME'] . $request_uri,
                        'time' => time()
                    ),
                'items_count' => array(
                        'available' => count($picked_items),
                        'returned' => count($items)
                    ),
                'items' => $items
                );
        break;

        case 'LookUpItem':
            //GETパラメーターのバリデーション 
            $params = $uri->validateLookUpItemParam($req_params);

            if ($params === false) {
                //GETのパラメーターが間違っている
                //400 Bad Request
                throw new BadRequestException('GETのパラメーターが間違っています');
            }

            //指定されたproduct_idに合う商品データをCSVから取得
            $picked_item = $csv->pickUpRecordById($params['product_id']);

            if (!is_array($picked_item)) {
                //返り値が配列ではない
                //500 Internal Server Error
                throw new Exception ('商品データの返り値が不正');
            }

            $item = isset($picked_item[0]) ? $picked_item[0] : array();

            //出力するエラー内容をセット
            $response['result'] = array(
                'requested' => array(
                        'action' => $action,
                        'format' => $format,
                        'parameter' => $req_params,
                        'url' => 'http://' . $_SERVER['SERVER_NAME'] . $request_uri,
                        'time' => time()
                    ),
                'item' => $item
                );
        break;

        default:
            //404 NOT FOUND
            throw new NotFoundException('そのようなAPIのアクションはありません');
        break;
    }

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
} catch (Exception $e) {
    // 500 Internal Server Error
    header("HTTP/1.1 500 Internal Server Error");
    $response['error'] = array(
            'code' => '500',
            'message' => 'Server Error'
        );
}

//レスポンス出力
if ($format == 'xml') {
    //xml形式で出力、PEARパッケージのXML_Serializer(http://pear.php.net/manual/ja/package.xml.xml-serializer.php)を使用
    require_once("XML/Serializer.php"); 

    if (isset($response['result'])) {
        $options = array( 
            XML_SERIALIZER_OPTION_INDENT => "\t", 
            XML_SERIALIZER_OPTION_XML_ENCODING => 'UTF-8', 
            XML_SERIALIZER_OPTION_XML_DECL_ENABLED => TRUE, 
            XML_SERIALIZER_OPTION_ROOT_NAME => 'result', 
            XML_SERIALIZER_OPTION_ROOT_ATTRIBS => array(), 
            XML_SERIALIZER_OPTION_DEFAULT_TAG => 'item'
        );

        $serializer = new XML_Serializer($options); 
        $serializer->serialize($response['result']); 
        $xml = $serializer->getSerializedData(); 
    
    } elseif (isset($response['error'])) {
        $options = array( 
            XML_SERIALIZER_OPTION_INDENT => "\t", 
            XML_SERIALIZER_OPTION_XML_ENCODING => 'UTF-8', 
            XML_SERIALIZER_OPTION_XML_DECL_ENABLED => TRUE, 
            XML_SERIALIZER_OPTION_ROOT_NAME => 'error', 
            XML_SERIALIZER_OPTION_ROOT_ATTRIBS => array(),
        );

        $serializer = new XML_Serializer($options); 
        $serializer->serialize($response['error']); 
        $xml = $serializer->getSerializedData(); 
    }

    header("Content-Type: text/xml; charset=utf-8"); 
    echo $xml;

} else {
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($response);
}

?>