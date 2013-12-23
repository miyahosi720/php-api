<?php

try {

    require('ApiServer.php');

    $api_server = new ApiServer();

    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        //405 Method Not Allowed
        $response = $api_server->render405Response();
    }

    $format = isset($_GET['format']) ? $_GET['format'] : '';

    switch ($_SERVER['PATH_INFO']) {
        case '/items':
            $response = $api_server->searchItems($_GET, $format);
            break;

        case '/item':
            $response = $api_server->getItemDetail($_GET, $format);
            break;

        default:
            //404 Not Found
            $response = $api_server->render404Response($format);
            break;
    }

} catch (Exception $e) {
    //500 Internal Server Error
    $response = $api_server->render500Response($format);

}

echo $response;


