<?php

require_once (dirname(__FILE__) . "/../models/item.php");
require_once (dirname(__FILE__) . "/base_controller.php");

class ItemsController extends Base_Controller
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
            $response_array = $this->_item->getItemSearchResponseArray($params);

            //Viewに渡す変数を設定
            /*
            $parameter = $response_array['result']['requested']['parameter'];
            $timestamp = $response_array['result']['requested']['timestamp'];
            $item_count = $response_array['result']['item_count'];
            $items = $response_array['result']['items'];
            */
            if ($params['format'] == 'xml') {
                require '../app/views/items/search.xml.php';
            } else {
                require '../app/views/items/search.json.php';
            }

        }

    }

    /**
     * 商品詳細API /item/#{id}
     * 未着手。。。
     */
    public function lookup($id)
    {

        if (!$this->_item->isNaturalNumber($id)) {
            //idが自然数でない、urlが不正
            $this->render400Page();
        }

        $response_array = $this->_item->getItemDetailResponseArray($id);

        require '../app/views/items/lookup.json.php';
    }
}