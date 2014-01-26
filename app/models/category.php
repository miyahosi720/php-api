<?php

require_once (dirname(__FILE__) . "/base_model.php");

class Category extends Base_Model
{
    public function getCategoryInfo($id)
    {
        $sql = "SELECT * FROM categories WHERE id = :id LIMIT 1";
        $placeholders[':id'] = $id;

        $category_record = $this->fetchAll($sql, $placeholders);

        if (empty($category_record)) {
            return array();
        } else {
            return $category_record[0];
        }
    }


}