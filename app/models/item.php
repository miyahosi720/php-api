<?php

require_once (dirname(__FILE__) . "/base_model.php");
require_once (dirname(__FILE__) . "/category.php");

class Item extends Base_Model
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

    /**
     * 商品検索のGETパラメーターのバリデーション
     * @param array GETパラメーター
     * @return mixed バリデーションでOKならばパラメータを返す、NGならばfalseを返す
     * @author miyahosi720
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

        //price_min
        if (!empty($params['price_min'])) {
            if (!$this->isNaturalNumber($params['price_min'])) {
                //price_minに自然数以外の値が入っている
                return false;
            }
        }

        //price_max
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

    /**
     * 商品検索で出力する情報のセットを配列で返す
     * @param array $params リクエストパラメータ
     * @return array 出力する情報の配列
     */
    public function getItemSearchResponseArray($params)
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

    /**
     * GETパラメーターからSQL文をbuild, executeし商品情報を取得
     * @param array $params リクエストパラメータ
     * @return array DBから取得した内容
     * @author miyahosi720
     */
    private function fetchItemsFromDb($params)
    {
        //SELECT文をbuild

        $placeholders = array();

        $where_array = array('TRUE');
        $where_str = ''; //WHERE 部分
        $order_str = ''; //ORDER BY 部分
        $limit_str = ''; //LIMT 部分
        $offset_str = ''; //OFFSET 部分

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

        }

        if (!empty($params['count_per_page']) && !empty($params['page_number'])) {

            $limit_str = "LIMIT :limit_count";
            $placeholders[':limit_count'] = $params['count_per_page'];

            $offset_str = "OFFSET :offset_count";
            $placeholders[':offset_count'] = $params['count_per_page'] * ($params['page_number'] - 1);
        }

        $sql = "SELECT * FROM items WHERE {$where_str} {$order_str} {$limit_str} {$offset_str}";

        //DBでSELECT文を発行、商品情報を取得

        $items = $this->fetchAll($sql, $placeholders);

        return $items;
    }

    /**
     * 商品詳細で出力する情報のセットを配列で返す
     * @param int $id リクエスト商品ID
     * @return array 出力する情報の配列
     */
    public function getItemDetailResponseArray($id)
    {
        $item_info = $this->getItemInfo($id);

        if (empty($item_info)) {
                $response_array['result'] = array(
                'requested' => array(
                        'id' => $id,
                        'timestamp' => time()
                    ),
                'item_hit' => 0,
                'item' => array(),
            );

            return $response_array;
        }

        $_category = new Category();
        $category_info = $_category->getCategoryInfo($item_info['category_id']);

        $parent_category_id = '';
        $parent_category_name = '';

        if (!empty($category_info['parent_id'])) {

            $parent_category_info = $_category->getCategoryInfo($category_info['parent_id']);

            $parent_category_id = $parent_category_info['id'];
            $parent_category_name = $parent_category_info['name'];
        }

        $item = array(
            'id' => $item_info['id'],
            'category' => array(
                'id' => $category_info['id'],
                'name' => $category_info['name'],
                'parent' => array(
                    'id' => $parent_category_id,
                    'name' => $parent_category_name),
                ),
            'title' => $item_info['title'],
            'price' => $item_info['price'],
            );

        $response_array['result'] = array(
            'requested' => array(
                    'id' => $id,
                    'timestamp' => time()
                ),
            'item_hit' => 1,
            'item' => $item
            );

        return $response_array;
    }

    /**
     * SQL文をbuild, executeし商品情報を取得
     * @param int $id リクエスト商品ID
     * @return array DBから取得した内容
     * @author miyahosi720
     */
    public function getItemInfo($id)
    {
        $sql = "SELECT * FROM items WHERE id = :id LIMIT 1";
        $placeholders[':id'] = $id;

        $item_record = $this->fetchAll($sql, $placeholders);

        if (empty($item_record)) {
            return array();
        } else {
            return $item_record[0];
        }

    }

    /**
     * PHPUnitテストの動作テスト用メソッド(笑)
     * @param mixed なんでも
     * @return mixed 右から左に受け流すお仕事
     * @author miyahosi720
     */
    private function hello($params)
    {
        return $params;
    }

}