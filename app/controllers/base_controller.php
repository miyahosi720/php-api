<?php

require_once (dirname(__FILE__) . "/../models/item.php");
require_once (dirname(__FILE__) . "/../models/category.php");
require_once (dirname(__FILE__) . "/../core/controllers/rest_controller.php");

class Base_Controller
{
    protected $_item;
    protected $_category;

    public function __construct()
    {
        $this->_item = new Item();
        $this->_category = new Category();
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


}
