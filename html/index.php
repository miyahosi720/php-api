<?php

require '../app/controllers/items_controller.php';

//var_dump($_SERVER['REQUEST_URI']);
//var_dump(dirname($_SERVER['REQUEST_URI']));
//var_dump($_SERVER['PATH_INFO']);
//var_dump(dirname($_SERVER['PATH_INFO']));
//var_dump($_GET);

if (empty($_SERVER['PATH_INFO'])) {
    phpinfo();
    exit;

} elseif (strpos(ltrim($_SERVER['PATH_INFO'], '/'), '/') === false) {
    if ($_SERVER['PATH_INFO'] == '/items') {
        //商品検索
        $controller = new ItemsController();
        $controller->index($_GET);
    } else {
        //404 Not Found
        echo '404desu';
    }

} else {

    if (dirname($_SERVER['PATH_INFO']) == '/item') {
        //商品詳細
        $uri_parameter = ltrim($_SERVER['PATH_INFO'], '/item');
        $controller = new ItemsController();
        $controller->show($uri_parameter);
    } else {
        //404 Not Found
        echo '404desu';
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