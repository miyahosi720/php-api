<?php

require '../app/models/item.php';

class ItemsController
{
    /**
     * GET /items
     *
     */
    public function index($params)
    {
        $action_name = 'index';
        /* echo "ItemsController#show is called<br/>"; */

        $item = new Item();
        $word = $item->hello();

        //パラメータのvalidation
        $params = $item->validateSearchParams($request_params);

        if ($params == false) {
            //GETパラメータが不正
        }

        //商品の検索結果
        $items_info = $item->getItemsInfo($params);

        require '../app/views/items/index.json.php';
    }

    /**
     * GET /items/#{id}
     *
     */
    public function show($params) {
        $action_name = 'show';
        /* echo "ItemsController#show is called<br/>"; */

        $item = new Item();
        $word = $item->hello();

        require '../app/views/items/show.json.php';
    }
}