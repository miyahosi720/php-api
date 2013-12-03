<?php
header("Content-Type: application/json; charset=utf-8");
/*
$csv  = array();
$file = 'item.csv';
$fp   = fopen($file, "r");
 
while (($data = fgetcsv($fp, 0, ",")) !== FALSE) {
  $csv[] = $data;
}
fclose($fp);
 
var_dump($csv);
*/

$file = 'item.csv';
$source = trim(file_get_contents($file));
$records = explode("\n", $source);

$items = array();

foreach ($records as $record) {
    list($product_id, $category_id, $title, $price) = explode(",", $record);

    $item['product_id'] = $product_id;
    $item['category_id'] = $category_id;
    $item['title'] = $title;
    $item['price'] = $price;

    $items[] = $item;

}

//var_dump($items);

$response['item'] = $items;

echo json_encode($response);