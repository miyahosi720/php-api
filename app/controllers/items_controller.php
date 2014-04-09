<?php

require_once (dirname(__FILE__) . "/base_controller.php");

class ItemsController extends Base_Controller
{
    /**
     * 商品検索API /items
     * GETパラメーターを元に検索を行い、結果の変数をViewに渡す
     * @param array $request_params GETパラメータの配列
     * @author miyahosi720
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

            //@ToDo: クラスを呼んで配列をjsonやxmlにConvert
            //@ToDo: json形式しか書いてないので、xml変換も書く
            $data['response'] = json_encode($response_array);

            View::render('items/search', $data);

        }

    }

    /**
     * 商品詳細API /item/#{id}
     * IDを元に商品の情報を取得し、結果の変数をViewに渡す
     * @param int $id リクエストされた商品ID
     * @author miyahosi720
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