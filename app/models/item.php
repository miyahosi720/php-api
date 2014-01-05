<?php

require_once (dirname(__FILE__) . "/../core/dbmanager.php");

class Item
{
    //デフォルトのパラメーター
    protected $params = array(
                'format' => 'json',
                'category_id' => '',
                'price_min' => '',
                'price_max' => '',
                'sort' => '',
                'count_per_page' => '',
                'page_number' => ''
                );

    /*
     * 商品検索のGETパラメーターをチェックする
     */
    public function validateSearchParams($request_params)
    {
        $params = $this->params;

        foreach ($request_params as $key => $value) {
            switch ($key) {
                case 'format' :
                case 'category_id' :
                case 'price_min':
                case 'price_max':
                case 'sort':
                case 'count_per_page':
                case 'page_number':

                    $params[$key] = $request_params[$key];
                    break;
                default :
                    //規定外のパラメーターが存在
                    return false;
                    break;
            }
        }

        //format
        if (!empty($params['format'])) {
            switch ($params['format']) {
                case 'json' :
                case 'xml' :
                break;
                default :
                    //formatの指定が正しくない
                    return false;
                break;
            }
        }

        //category_id
        if (!empty($params['category_id'])) {
            if (!$this->isNaturalNumber($params['category_id'])) {
                return false;
            }
        }

        //price_max
        if (!empty($params['price_min'])) {
            if (!$this->isNaturalNumber($params['price_min'])) {
                //price_minに自然数以外の値が入っている
                return false;
            }
        }

        //price_min
        if (!empty($params['price_max'])) {
            if (!$this->isNaturalNumber($params['price_max'])) {
                //price_maxに自然数以外の値が入っている
                return false;
            }
        }

        //price_minとprice_maxの大小関係
        if (!empty($params['price_min']) && !empty($params['price_max']) && $params['price_min'] > $params['price_max']) {
            //price_minがprice_maxより大きい
            return false;
        }

        //sort
        if (!empty($params['sort'])) {
            switch ($params['sort']) {
                case '+id' :
                case '-id' :
                case '+price' :
                case '-price' :
                break;
                default :
                    //sortの指定が正しくない
                    return false;
                break;
            }
        }

        //count_per_pageとpage_number
        if ((empty($params['count_per_page']) && !empty($params['page_number'])) || (!empty($params['count_per_page']) && empty($params['page_number']))) {
            //count_per_pageとpage_numberのどちらか一方のみがセットされている
            return false;

        } elseif (!empty($params['count_per_page']) && !empty($params['page_number'])) {
            if (!$this->isNaturalNumber($params['count_per_page'])) {
                //count_per_pageに自然数以外の値が入っている
                return false;
            }
            if (!$this->isNaturalNumber($params['page_number'])) {
                //page_numberに自然数以外の値が入っている
                return false;
            }
        }

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

    /*
     * 出力する情報のセットを配列で返す
     */
    public function createResultResponseArray($params)
    {

        $items = $this->fetchItemsFromDb($params);

        $response_array['result'] = array(
            'requested' => array(
                    'parameter' => $_GET,
                    'timestamp' => time()
                ),
            'item_count' => count($items),
            'items' => $items
            );

        return $response_array;
    }

    /*
     * GETパラメーターからSQL文をbuild, executeし商品情報を取得
     */
    private function fetchItemsFromDb($params)
    {
        //SQL文をbuild

        $placeholders = array();

        $where_array = array('TRUE');

        if (!empty($params['category_id'])) {
            $where_array[] = 'category_id = :category_id';
            $placeholders[':category_id'] = $params['category_id'];
        }

        if (!empty($params['price_min'])) {
            $where_array[] = 'price >= :price_min';
            $placeholders[':price_min'] = $params['price_min'];
        }

        if (!empty($params['price_max'])) {
            $where_array[] = 'price <= :price_max';
            $placeholders[':price_max'] = $params['price_max'];
        }

        $where_str = implode(' AND ', $where_array);

        if (!empty($params['sort'])) {
            switch ($params['sort']) {
                case '+id' :
                    $order_str = "ORDER BY id ASC";
                    break;
                case '-id' :
                    $order_str = "ORDER BY id DESC";
                    break;
                case '+price' :
                    $order_str = "ORDER BY price ASC";
                    break;
                case '-price' :
                    $order_str = "ORDER BY price DESC";
                    break;
            }

        } else {
            $order_str = "";
        }

        if (!empty($params['count_per_page']) && !empty($params['page_number'])) {

            $limit_str = "LIMIT :limit_count";
            $placeholders[':limit_count'] = $params['count_per_page'];

            $offset_str = "OFFSET :offset_count";
            $placeholders[':offset_count'] = $params['count_per_page'] * ($params['page_number'] - 1);

        } else {
            $limit_str = "";
            $offset_str = "";
        }

        $sql = "SELECT * FROM items WHERE {$where_str} {$order_str} {$limit_str} {$offset_str}";

        //DBでSELECT文を発行、商品情報を取得
        $dbmanager = new DbManager();

        $items = $dbmanager->fetchAll($sql, $placeholders);

        return $items;
    }

    /*
     * 400エラーの際に出力するコードとメッセージを設定
     */
    public function create400ErrorResponseArray()
    {
        $response_array['error'] = array(
            'code' => '400',
            'message' => 'Requested parameter is not valid'
            );
        return $response_array;
    }

    public function create404ErrorResponseArray()
    {
        $response_array['error'] = array(
            'code' => '404',
            'message' => 'The url you requested was not found'
            );
        return $response_array;
    }

    public function create405ErrorResponseArray()
    {
        $response_array['error'] = array(
            'code' => '405',
            'message' => 'Your HTTP method is not allowed'
        );
        return $response_array;
    }

    public function create500ErrorResponseArray()
    {
        $response_array['error'] = array(
            'code' => '500',
            'message' => 'Server Error'
        );
        return $response_array;
    }

    private function hello($params)
    {
        return $params;
    }

}