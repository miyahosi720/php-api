<?php

if (!isset($_SERVER['PATH_INFO'])) {
    phpinfo();
    exit;
}

require '../app/core/request.php';
require '../app/controllers/items_controller.php';

$request = new Request();
$controller = new ItemsController();

$uri_segments = $request->resolvePathInfo($_SERVER['PATH_INFO']);

if (count($uri_segments) == 1 && $uri_segments[0] == 'items') {

    if ($request->isGet()) {
        //商品検索API
        $controller->search($_GET);
    } else {
        //405 Method Not Allowed
        $controller->render405Page();
    }

} elseif (count($uri_segments) == 2 && $uri_segments[0] == 'item') {

    if ($request->isGet()) {
        //商品詳細API
        $controller->lookup($uri_segments[1]);
    } else {
        //405 Method Not Allowed
        $controller->render405Page();
    }

} else {
    //404 Not Found
    $controller->render404Page();
}