<?php
require '../app/core/request.php';
require '../app/controllers/items_controller.php';

$controller = new ItemsController();
$request = new Request();

$uri_segments = $request->resolvePathInfo($_SERVER['PATH_INFO']);

//var_dump($_SERVER['REQUEST_URI']);
//var_dump(dirname($_SERVER['REQUEST_URI']));
//var_dump($_SERVER['PATH_INFO']);
//var_dump(dirname($_SERVER['PATH_INFO']));
//var_dump($_GET);

if (count($uri_segments) == 1 && $uri_segments[0] == 'items') {

    if ($request->isGet()) {
        $controller->search($_GET);
    } else {
        $controller->render405Page();
    }

} elseif (count($uri_segments) == 2 && $uri_segments[0] == 'item') {

    if ($request->isGet()) {
        $controller->lookup($uri_segments[1]);
    } else {
        $controller->render405Page();
    }

} else {
    $controller->render404Page();
}

/*


if (empty($_SERVER['PATH_INFO'])) {
    phpinfo();
    exit;

} elseif (strpos(ltrim($_SERVER['PATH_INFO'], '/'), '/') === false) {

    if ($_SERVER['PATH_INFO'] == '/items') {
        //商品検索API
        $controller->search($_GET);

    } else {
        //404 Not Found
        $controller->render404Page();
    }

} else {

    if (dirname($_SERVER['PATH_INFO']) == '/item') {
        //商品詳細API
        $uri_parameter = ltrim($_SERVER['PATH_INFO'], '/item/');

        $controller->lookup($uri_parameter);

    } else {
        //404 Not Found
        $controller->render404Page();
    }
}

/*
switch($requested_action) {

    case '/items' :
        $controller = new ItemsController();
        $controller->index($_GET);
        break;

    case '/item' :
        $controller = new ItemsController();
        $controller->show($uri_parameter);
        break;

    default :
        break;
}
*/