<?php

require_once (dirname(__FILE__) . "/base_model.php");

class Category extends Base_Model
{
    public function getCategoriesListResponseArray()
    {
        $all_categories = $this->getAllCategories();

        $parent = array();
        $child = array();

        foreach ($all_categories as $category) {
            if (empty($category['parent_id'])) {
                unset($category['parent_id']);
                $parent[] = $category;
            } else {
                $child[] = $category;
            }
        }

        $response_array['result'] = array(
            'requested' => array(
                'timestamp' => time()
                ),
            'categories' => array(
                    'parent' => $parent,
                    'child' => $child,
                ),
            );

        return $response_array;

    }

    public function getCategoryInfo($id)
    {
        $sql = "SELECT * FROM categories WHERE id = :id LIMIT 1";
        $placeholders[':id'] = $id;

        $result = $this->fetchAll($sql, $placeholders);

        if (empty($result)) {
            return array();
        } else {
            return $result[0];
        }
    }

    public function getAllCategories()
    {
        $sql = "SELECT * FROM categories";

        $all_categories = $this->fetchAll($sql);

        return $all_categories;
    }

}