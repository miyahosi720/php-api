<?php

class Csv
{
    protected $file = 'item.csv';
    protected $records;

    public function __construct()
    {
        $file = $this->file;
        $source = trim(file_get_contents($file));
        $this->records = explode("\n", $source);
    }

    /*
     * カテゴリID・価格範囲に合う商品レコードをCSVからパースし配列で返す
     */
    public function pickUpRecordsByConditions($selected_category_id = '', $price_min = '', $price_max = '')
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

    private function isPriceInRange($price, $price_min = '', $price_max = '')
    {
        if ((!empty($price_min) && (int)$price < (int)$price_min) || (!empty($price_max) && (int)$price_max < (int)$price)) {
            return false;
        }

        return true;
    }

    private function isCategoryMatched($category_id, $selected_category_id = '')
    {
        if (!empty($selected_category_id) && (int)$category_id != (int)$selected_category_id) {
            return false;
        }

        return true;
    }

    public function sort($items, $selected_sort = '')
    {
        if (!empty($selected_sort)) {
            switch ($selected_sort) {
                case 'id_desc' :
                    usort($items,  array($this, "id_desc"));
                break;
                case 'id_asc' :
                    usort($items,  array($this, "id_asc"));
                break;
                case 'price_desc' :
                    usort($items,  array($this, "price_desc"));
                break;
                case 'price_asc' :
                    usort($items,  array($this, "price_asc"));
                break;
                default :
                return false;
            }
        }

        return $items;

    }

    private function func_caller($name) {
        if (function_exists($name)){
            $name();
        }
    }


    private function price_desc($a, $b)
    {
        return $b['price'] - $a['price'];
    }

    private function price_asc($a, $b)
    {
        return $a['price'] - $b['price'];
    }

    private function id_desc($a, $b)
    {
        return $b['product_id'] - $a['product_id'];
    }

    private function id_asc($a, $b)
    {
        return $a['product_id'] - $b['product_id'];
    }

    public function pagination($items, $count_per_page = '', $page_number = '')
    {
        if (!empty($count_per_page) && !empty($page_number)) {
            $offset = $count_per_page * ($page_number - 1);
            $limit = $count_per_page;

            $items = array_slice($items, $offset, $limit);
        }

        return $items;
    }

    /*
     * 商品IDに合致する商品の情報をCSVから取得し、返す
     */
    public function pickUpRecordById($selected_product_id)
    {
        $items = array();

        foreach ($this->records as $record) {
            list($product_id, $category_id, $title, $price) = explode(",", $record);

            $product_id_matched = $this->isProductIdMatched($product_id, $selected_product_id);

            if ($product_id_matched) {
                $item['product_id'] = $product_id;
                $item['category_id'] = $category_id;
                $item['title'] = $title;
                $item['price'] = $price;

                $items[] = $item;
                return $items;
            }
        }

        return array();
    }

    private function isProductIdMatched($product_id, $selected_category_id)
    {
        if ((int)$product_id == (int)$selected_category_id) {
            return true;
        }

        return false;
    }

}