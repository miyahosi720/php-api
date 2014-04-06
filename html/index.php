<?php

if (!isset($_SERVER['PATH_INFO'])) {
    phpinfo();
    exit;
}

require '../app/core/request.php';
require '../app/controllers/items_controller.php';
require '../app/controllers/categories_controller.php';

$request = new Request();
$items = new ItemsController();
$categories = new CategoriesController();

$uri_segments = $request->resolvePathInfo($_SERVER['PATH_INFO']);

if (count($uri_segments) == 1 && $uri_segments[0] == 'items') {

    if ($request->isGet()) {
        //商品検索API
        $items->searchItems($_GET);
    } else {
        //405 Method Not Allowed
        $items->render405Page();
    }

} elseif (count($uri_segments) == 2 && $uri_segments[0] == 'item') {

    $items->lookUpItem($uri_segments[1]);

} elseif (count($uri_segments) == 1 && $uri_segments[0] == 'categories') {

    //カテゴリ一覧
    $categories->listCategories();

} elseif (count($uri_segments) == 2 && $uri_segments[0] == 'category') {

    //特定カテゴリの情報&商品を返す
    $categories->lookUpCategory($uri_segments[1], $_GET);

} else {
    //404 Not Found
    $items->render404Page();
}
