<?php

require_once (dirname(__FILE__) . "/base_controller.php");

class CategoriesController extends Base_Controller
{
    public function listCategories()
    {
        $response_array = $this->_category->getCategoriesListResponseArray();

        require '../app/views/categories/index.json.php';
    }

    public function lookUpCategory($id, $parent)
    {
        $response_array = array('sorry' => 'timeup');
        require '../app/views/categories/lookup.json.php';
    }
}