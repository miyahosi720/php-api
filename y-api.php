<?php

require('search.php');

header("Content-Type: application/json; charset=utf-8");

$response = array();

$pos = strpos($_SERVER['REQUEST_URI'], '?');

$base_url = substr($_SERVER['REQUEST_URI'], 0, $pos);

var_dump($_SERVER['REQUEST_URI']);
var_dump($base_url);

switch ($base_url) {

    case '/y-api/items/search':

        echo '/y-api/items/searchだよ';

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

    case '/y-api/item/view':

        echo '/y-api/item/viewだよ';

    break;

    default:
        //エラーとエラーコード、エラーメッセージ（リクエストは成功している）
        $request['error'] = array (
            'message' => 'Bad Request',
            'code' => '400'
        );

    break;
}

//echo json_encode($response);
?>