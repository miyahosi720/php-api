<?php

$price_min = 1000; //最低価格
$price_max = 2000; //最高価格

$file = 'item.csv';
$source = trim(file_get_contents($file));
$records = explode("\n", $source);

$items = array();

foreach ($records as $record) {
    list($product_id, $category_id, $title, $price) = explode(",", $record);

    $result = selectByPrice($price, $price_min, $price_max);

    if ($result) {
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
usort($items, "comp");

function comp($price1, $price2)
{
    $a = $price1['price'];
    $b = $price2['price'];

    return $b - $a;
}

var_dump($items);

/*
 * $priceが$price_min以上、$price_max以下ならばtrue, それ以外はfalseを返す
 */
function selectByPrice($price, $price_min, $price_max)
{
    if ((!empty($price_min) && $price < $price_min) || (!empty($price_max) && $price_max < $price)) {
        return false;
    }

    return true;
}

/*ToDo
 * 指定した価格以上・以下のみを返す→ほぼOK
 * ソート→着手
 * ページング （1ページあたりの商品数、表示するページNo）→これから



