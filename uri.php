<?php

class Uri
{
    /*
     * REQUEST_URIからactionとformatを抽出する
     * "/y-api/v1/SearchItems.json" のようなURIであれば、SearchItemsがaction, jsonがformat
     * URIの形式が間違っていればfalseを返す
     */
    public function extractRequestedActionAndFormat($request_uri)
    {
        // request_uri先頭の/y-api/v1/（10文字）は共通なので、切り出す
        $base_uri = substr($request_uri, 10);

        if ($base_uri === false) { 
            //request_uriが/y-api/v1/の場合
            return false;
        } else {

            //urlに?が含まれていたら、?より前の箇所を抜き出す。これをcore_uriと呼ぶ
            // uriが/y-api/v1/SearchItems.json?sort=id_descのとき、core_uriはSearchItems.json
            if (false !== $pos = strpos($base_uri, '?')) {
                $core_uri = substr($base_uri, 0, $pos);
            } else {
                $core_uri = $base_uri;
            }
        }

        //core_uriを.で分割
        $pieces = explode(".", $core_uri);

        if (count($pieces) != 2) {
            //core_uriに含まれるドットが1つではない、エラー
            return false;
        }

        $action = $pieces[0];
        $format = $pieces[1];

        if ($format != 'json' && $format != 'xml') {
            //.jsonまたは.xml以外の指定、エラー
            return false;
        }

        $requested = array();
        $requested['action'] = $action;
        $requested['format'] = $format;

        return $requested;
    }

    /*
     * SerchItemsのGETパラメーターをチェックする
     */
    public function validateSearchItemsParams($req_params)
    {
        //不必要なパラメーターチェック
        foreach ($req_params as $key => $value) {
            switch ($key) {
                case 'category_id' :
                case 'price_min':
                case 'price_max':
                case 'sort':
                case 'count_per_page':
                case 'page_number':
                    break;
                default :
                    //規定外のパラメーターが存在
                    return false;
                    break;
            }
        }

        $category_id = isset($req_params['category_id']) ? trim($req_params['category_id']) : '';
        $price_min = isset($req_params['price_min']) ? trim($req_params['price_min']) : '';
        $price_max = isset($req_params['price_max']) ? trim($req_params['price_max']) : '';
        $sort = isset($req_params['sort']) ? trim($req_params['sort']) : '';
        $count_per_page = isset($req_params['count_per_page']) ? trim($req_params['count_per_page']) : '';
        $page_number = isset($req_params['page_number']) ? trim($req_params['page_number']) : '';

        //category_id
        if (!empty($category_id)) {
            if (!$this->isNaturalNumber($category_id)) {
                return false;
            }
        }

        //price_max
        if (!empty($price_min)) {
            if (!$this->isNaturalNumber($price_min)) {
                //price_minに自然数以外の値が入っている
                return false;
            }
        }

        //price_min
        if (!empty($price_max)) {
            if (!$this->isNaturalNumber($price_max)) {
                //price_maxに自然数以外の値が入っている
                return false;
            }
        }

        //price_minとprice_maxの大小関係
        if (!empty($price_min) && !empty($price_max) && $price_min > $price_max) {
            //price_minがprice_maxより大きい
            return false;
        }

        //sort
        if (!empty($sort)) {
            switch ($sort) {
                case 'id_desc' :
                case 'id_asc' :
                case 'price_desc' :
                case 'price_asc' :
                break;
                default :
                    //sortの指定が正しくない
                    return false;
                break;
            }
        }

        //count_per_pageとpage_number
        if ((empty($count_per_page) && !empty($page_number)) || (!empty($count_per_page) && empty($page_number))) {
            //count_per_pageとpage_numberのどちらか一方のみがセットされている
            return false;

        } elseif (!empty($count_per_page) && !empty($page_number)) {
            if (!$this->isNaturalNumber($count_per_page)) {
                //count_per_pageに自然数以外の値が入っている
                return false;
            }
            if (!$this->isNaturalNumber($page_number)) {
                //page_numberに自然数以外の値が入っている
                return false;
            }
        }

        $params = array(
                'category_id' => $category_id,
                'price_min' => $price_min,
                'price_max' => $price_max,
                'sort' => $sort,
                'count_per_page' => $count_per_page,
                'page_number' => $page_number
            );

        return $params;
    }

    /*
     * LookUpItemのGETパラメーターをチェックする
     */
    public function validateLookUpItemParam($req_params)
    {
        if (!isset($req_params['product_id']) || (!$this->isNaturalNumber($req_params['product_id']))) {
            return false;
        }

        //不必要なパラメーターチェック
        foreach ($req_params as $key => $value) {
            switch ($key) {
                case 'product_id' :
                    break;
                default :
                    //規定外のパラメーターが存在
                    return false;
                    break;
            }
        }

        $params['product_id'] = $req_params['product_id'];

        return $params;
    }

    /*
     * 値が自然数かどうかをチェックする
     */
    private function isNaturalNumber($string)
    {
        if (is_numeric($string) && 0 < (int)$string) {
            return true;
        }

        return false;
    }

}