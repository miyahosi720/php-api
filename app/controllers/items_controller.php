<?php

require_once (dirname(__FILE__) . "/../models/item.php");

class ItemsController
{
    protected $_item;

    public function __construct()
    {
        $this->_item = new Item();
    }

    /**
     * 商品検索API /items
     */
    public function search($request_params)
    {
        //GETパラメータのvalidation
        $params = $this->_item->validateSearchParams($request_params);

        if ($params == false) {
            //GETパラメータが不正 400 Bad Request
            $this->render400Page();

        } else {
            $response_array = $this->_item->createResultResponseArray($params);

            //Viewに渡す変数を設定
            $parameter = $response_array['result']['requested']['parameter'];
            $timestamp = $response_array['result']['requested']['timestamp'];
            $item_count = $response_array['result']['item_count'];
            $items = $response_array['result']['items'];

            if ($params['format'] == 'xml') {
                require '../app/views/items/search.xml.php';
            } else {
                require '../app/views/items/search.json.php';
            }

        }

    }

    public function render400Page()
    {
        $response_array = $this->_item->create400ErrorResponseArray();

        //Viewに渡す変数を設定
        $error_code = $response_array['error']['code'];
        $error_message = $response_array['error']['message'];

        header("HTTP/1.1 400 Bad Request");
        require '../app/views/items/error.json.php';
    }

    public function render404Page()
    {
        $response_array = $this->_item->create404ErrorResponseArray();

        //Viewに渡す変数を設定
        $error_code = $response_array['error']['code'];
        $error_message = $response_array['error']['message'];

        header("HTTP/1.1 404 Not Found");
        require '../app/views/items/error.json.php';
    }

    public function render405Page()
    {
        $response_array = $this->_item->create405ErrorResponseArray();

        //Viewに渡す変数を設定
        $error_code = $response_array['error']['code'];
        $error_message = $response_array['error']['message'];

        header("HTTP/1.1 405 Method Not Allowed");
        require '../app/views/items/error.json.php';
    }

    public function render500Page()
    {
        $response_array = $this->_item->create500ErrorResponseArray();

        //Viewに渡す変数を設定
        $error_code = $response_array['error']['code'];
        $error_message = $response_array['error']['message'];

        header("HTTP/1.1 405 Method Not Allowed");
        require '../app/views/items/error.json.php';
    }

    /**
     * 商品詳細API /item/#{id}
     * 未着手。。。
     */
    public function lookup($params)
    {
        require '../app/views/items/lookup.json.php';
    }
}