<?php

require_once (dirname(__FILE__) . "/base_model.php");

class Category extends Base_Model
{
    /**
     * 全カテゴリの情報を取得
     * @return array カテゴリ一覧APIで返す内容の配列
     * @author miyahosi720
     */
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

    /**
     * 全カテゴリを取得するクエリの結果
     * @return array 全カテゴリ情報
     * @author miyahosi720
     */
    public function getAllCategories()
    {
        $sql = "SELECT * FROM categories";
        $key = md5($sql);

        $all_categories = $this->memcache->get($key);
        if (! $all_categories) {
            $all_categories = $this->fetchAll($sql);
            $this->memcache->set($key, $all_categories);
        }

        return $all_categories;
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

}
