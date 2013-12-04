<?php

$price_min = 1000; //最低価格
$price_max = null; //最高価格

$selected_category_id = 1000004; //指定カテゴリID

$count_per_page = null; //１ページあたりの商品数
$page_number = null; //今見ているページ数
//どちらか片方がセットされていたらエラーを返す？

$file = 'item.csv';
$source = trim(file_get_contents($file));
$records = explode("\n", $source);

$items = array();

foreach ($records as $record) {
    list($product_id, $category_id, $title, $price) = explode(",", $record);

    $price_in_range = isPriceInRange($price, $price_min, $price_max);

    $category_selected = isCategorySelected($category_id, $selected_category_id);

    if ($price_in_range && $category_selected) {
        $item['product_id'] = $product_id;
        $item['category_id'] = $category_id;
        $item['title'] = $title;
        $item['price'] = $price;

        $items[] = $item;
    }

/*
    if (条件) {
        商品が条件に合っていたら、レコードをitems配列に入れる
    }
    items配列が返る（条件に合った商品情報のみ入っている）
*/

}

//priceで降順ソート
usort($items, "id_desc");

function price_desc($a, $b)
{
    return $b['price'] - $a['price'];
}

function price_asc($a, $b)
{
    return $a['price'] - $b['price'];
}

function id_desc($a, $b)
{
    return $b['product_id'] - $a['product_id'];
}

function id_asc($a, $b)
{
    return $a['product_id'] - $b['product_id'];
}

var_dump($items);

//ページング
if (!empty($count_per_page) && !empty($page_number)) {
    $offset = $count_per_page * ($page_number - 1);
    $limit = $count_per_page;

    $paginated_items = array_slice($items, $offset, $limit);
}

//var_dump($paginated_items);


/*
 * $priceが$price_min以上、$price_max以下ならばtrue, それ以外はfalseを返す
 */
function isPriceInRange($price, $price_min, $price_max)
{
    if ((!empty($price_min) && $price < $price_min) || (!empty($price_max) && $price_max < $price)) {
        return false;
    }

    return true;
}

/*
 * $priceが$price_min以上、$price_max以下ならばtrue, それ以外はfalseを返す
 */
function isCategorySelected($category_id, $selected_category_id)
{
    if ($category_id == $selected_category_id) {
        return true;
    }

    return false;
}

/*ToDo
 * 指定した価格以上・以下のみを返す
 * 指定したカテゴリを返す
 * ソート→着手
 * ページング （1ページあたりの商品数、表示するページNo）



