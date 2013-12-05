<?php

require('search.php');

header("Content-Type: application/json; charset=utf-8");

$response = array();


$request_uri = $_SERVER['REQUEST_URI'];

var_dump($request_uri);

$base_url = substr($request_uri, 10); // /y-api/v1/を切り出し

var_dump($base_url);

if ($base_url === false) { 
    // /y-api/v1/の場合
    echo '/y-api/v1/です';
    exit;
} else {

    //urlに?が含まれていたら、?より前の箇所を抜き出す
    if (false !== $pos = strpos($base_url, '?')) {
        $core_url = substr($base_url, 0, $pos);
    } else {
        $core_url = $base_url;
    }
}

var_dump($core_url);

$pieces = explode(".", $core_url);
$action = $pieces[0];
$format = $pieces[1];

if (count($pieces) != 2) {
    //core_urlに含まれるドットが1つではない、エラー
    echo 'ドットが1つじゃないよ';
    exit;
} elseif ($format == 'json') {
    //json形式のフラグを設定
    $format_json = 1;
} elseif ($format == 'xml') {
    //xml形式のフラグを設定
    $format_xml = 1;
} else {
    echo '.json（または.xml）直後がおかしい';
    exit;
}

switch ($action) {

    case 'SearchItems':

        echo 'SearchItemsだよ';

        //GETだったら
        var_dump($_GET);

        $selected_category_id = $_GET['category_id'];
        $price_min = $_GET['price_min'];
        $price_max = $_GET['price_max'];
        $selected_sort = $_GET['sort'];
        $count_per_page = $_GET['count_per_page'];
        $page_number = $_GET['page_number'];
        //validation
        //count_per_page, page_numberどちらかしかセットされていなかったらエラー
        //エラーはtry〜catch?

        $search = new Search();

        //カテゴリID、最低最高価格で限定してCSVからパース
        $parsed_ret = $search->parseCSV(null, null, 2000);
        //echo json_encode($parsed_ret);
        
        //ソート
        //ソート条件がないときはそもそもこの行を実行しないのがいい
        $sorted_ret = $search->sort($parsed_ret, 'price_desc');
        echo json_encode($sorted_ret);
        
        //ページネーション
        //$paginated_ret = $search->pagination($sorted_ret, $count_per_page, $page_number);
        $paginated_ret = $search->pagination($sorted_ret, 4, 3);
        //echo json_encode($paginated_ret);
/*
        $response['item'] = $items;
        $response['item_count'] = $item_count;
        $response['timestamp'] = time();
*/

        //GET以外のメソッドだったら→エラー
    break;

    case 'LookUpItem':

        echo 'LookUpItemだよ';

    break;

    default:
        echo '.json（または.xml）直前がおかしい';
        exit;
        //エラーとエラーコード、エラーメッセージ（リクエストは成功している）
        $request['error'] = array (
            'message' => 'Bad Request',
            'code' => '400'
        );

    break;
}

//echo json_encode($response);
?>