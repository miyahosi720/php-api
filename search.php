<?php

class Search
{
    protected $selected_category_id;
    protected $price_min;
    protected $price_max;
    protected $sort;
    protected $count_per_page;
    protected $page_number;

    /*
     * CSVのデータのうち、条件に該当するレコードをパースして配列として返す
     */
    public function pickUpRecordsFromCsv($selected_category_id = '', $price_min = '', $price_max = '')
    {
        $file = 'item.csv';
        $source = trim(file_get_contents($file));
        $records = explode("\n", $source);

        $items = array();

        foreach ($records as $record) {
            list($product_id, $category_id, $title, $price) = explode(",", $record);

            $category_selected = $this->isCategorySelected($category_id, $selected_category_id);

            $price_in_range = $this->isPriceInRange($price, $price_min, $price_max);

            if ($price_in_range && $category_selected) {
                $item['product_id'] = $product_id;
                $item['category_id'] = $category_id;
                $item['title'] = $title;
                $item['price'] = $price;

                $items[] = $item;
            }

        }

        return $items;

    }

    /*
     * $priceが$price_min以上、$price_max以下ならばtrue, それ以外はfalseを返す
     */
    private function isPriceInRange($price, $price_min = '', $price_max = '')
    {
        if ((!empty($price_min) && (int)$price < (int)$price_min) || (!empty($price_max) && (int)$price_max < (int)$price)) {
            return false;
        }

        return true;
    }

    /*
     * $category_idが$selected_category_idと一致していればtrue, それ以外はfalseを返す
     */
    private function isCategorySelected($category_id, $selected_category_id = '')
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





}