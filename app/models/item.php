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
}