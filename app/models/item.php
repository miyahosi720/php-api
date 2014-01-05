<?php

class Item
{
    protected $file = '../data/item.csv';
    protected $records;

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

    public function __construct()
    {
        $file = $this->file;
        $source = trim(file_get_contents($file));
        $this->records = explode("\n", $source);
    }

    public function hello()
    {
        return $this->records;
    }

    public function moe()
    {
        return 'item moe!';
    }

    /*
     * SerchItemsのGETパラメーターをチェックする
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

    public function createResultResponseArray($params)
    {
        //カテゴリID、価格範囲に合う商品データをCSVから取得
        $picked_items = $this->pickUpRecordsByConditions($params['category_id'], $params['price_min'], $params['price_max']);

        //ソート
        $sorted_items = $this->sort($picked_items, $params['sort']);

        //ページネーション
        $paginated_items = $this->pagination($sorted_items, $params['count_per_page'], $params['page_number']);

        $items = $paginated_items;

        $response_array['result'] = array(
            'requested' => array(
                    'parameter' => $_GET,
                    'timestamp' => time()
                ),
            'item_count' => array(
                    'returned' => count($items),
                    'available' => count($picked_items)
                ),
            'item' => $items
            );

        return $response_array;
    }

    /*
     * カテゴリID・価格範囲に合う商品レコードをCSVからパースし配列で返す
     */
    private function pickUpRecordsByConditions($selected_category_id = '', $price_min = '', $price_max = '')
    {
        $items = array();

        foreach ($this->records as $record) {
            list($product_id, $category_id, $title, $price) = explode(",", $record);

            $category_matched = $this->isCategoryMatched($category_id, $selected_category_id);

            $price_in_range = $this->isPriceInRange($price, $price_min, $price_max);

            if ($price_in_range && $category_matched) {
                $item['product_id'] = $product_id;
                $item['category_id'] = $category_id;
                $item['title'] = $title;
                $item['price'] = $price;

                $items[] = $item;
            }

        }

        return $items;
    }

    private function isCategoryMatched($category_id, $selected_category_id = '')
    {
        if (!empty($selected_category_id) && (int)$category_id != (int)$selected_category_id) {
            return false;
        }

        return true;
    }

    private function isPriceInRange($price, $price_min = '', $price_max = '')
    {
        if ((!empty($price_min) && (int)$price < (int)$price_min) || (!empty($price_max) && (int)$price_max < (int)$price)) {
            return false;
        }

        return true;
    }

    public function sort($items, $selected_sort = '')
    {
        if (!empty($selected_sort)) {

            $compare = function($a, $b) use ($selected_sort) {
                switch ($selected_sort) {
                    case '+id' :
                        return $a['product_id'] - $b['product_id'];
                    case '-id' :
                        return $b['product_id'] - $a['product_id'];
                    case '+price' :
                        return $a['price'] - $b['price'];
                    case '-price' :
                        return $b['price'] - $a['price'];
                    default :
                        return false;
                }
            };
            usort($items, $compare);

        }

        return $items;
    }

    private function pagination($items, $count_per_page = '', $page_number = '')
    {
        if (!empty($count_per_page) && !empty($page_number)) {
            $offset = $count_per_page * ($page_number - 1);
            $limit = $count_per_page;

            $items = array_slice($items, $offset, $limit);
        }

        return $items;
    }

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



}