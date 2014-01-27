<?php

require_once (dirname(__FILE__) . "/base_controller.php");

class ItemsController extends Base_Controller
{
    /**
     * 商品検索API /items
     */
    public function searchItems($request_params)
    {
        //GETパラメータのvalidation
        $params = $this->_item->validateSearchParams($request_params);

        if ($params == false) {
            //GETパラメータが不正 400 Bad Request
            $this->render400Page();

        } else {
            $response_array = $this->_item->getItemSearchResponseArray($params);

            if ($params['format'] == 'xml') {
                require '../app/views/items/search.xml.php';
            } else {
                require '../app/views/items/search.json.php';
            }
        }

    }

    /**
     * 商品詳細API /item/#{id}
     */
    public function lookUpItem($id)
    {
        if (!$this->_item->isNaturalNumber($id)) {
            //idが自然数でない、urlが不正
            $this->render400Page();
        } else {
            $response_array = $this->_item->getItemDetailResponseArray($id);

            require '../app/views/items/lookup.json.php';
        }
    }
}