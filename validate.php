<?php

class Validate
{
    public function validateGetParams($params)
    {
        $result = array();
        $error = array();

        //不必要なパラメーターチェック
        foreach ($params as $key => $value) {
            switch ($key) {
                case 'category_id' :
                case 'price_min':
                case 'price_max':
                case 'sort':
                case 'count_per_page':
                case 'page_number':
                    break;
                default :
                    //不必要なパラメーターが入っている
                    return false;
                    break;
            }
        }

        $category_id = isset($params['category_id']) ? trim($params['category_id']) : '';
        $price_min = isset($params['price_min']) ? trim($params['price_min']) : '';
        $price_max = isset($params['price_max']) ? trim($params['price_max']) : '';
        $sort = isset($params['sort']) ? trim($params['sort']) : '';
        $count_per_page = isset($params['count_per_page']) ? trim($params['count_per_page']) : '';
        $page_number = isset($params['page_number']) ? trim($params['page_number']) : '';

        //category_id
        if (!empty($category_id)) {
            if (!$this->isNaturalNumber($category_id)) {
                //category_idに自然数以外の値が入っている
                return false;
            } else {
                switch ($category_id) {
                    case 1000001 :
                    case 1000002 :
                    case 1000003 :
                    case 1000004 :
                    case 1000005 :
                    case 1000006 :
                    case 1000007 :
                    case 1000008 :
                    case 1000009 :
                    case 1000010 :
                    break;
                    default :
                        //該当するcategory_idが無い
                        return false;
                    break;
                }
            }
        }

        //price_max
        if (!empty($price_min)) {
            if (!$this->isNaturalNumber($price_min)) {
                //price_minに自然数以外の値が入っている
                return false;
            }
        }

        //price_min
        if (!empty($price_max)) {
            if (!$this->isNaturalNumber($price_max)) {
                //price_maxに自然数以外の値が入っている
                return false;
            }
        }

        //price_minとprice_maxの大小関係
        if (!empty($price_min) && !empty($price_max) && $price_min > $price_max) {
            //price_minがprice_maxより大きい
            return false;
        }

        //sort
        if (!empty($sort)) {
            switch ($sort) {
                case 'id_desc' :
                case 'id_asc' :
                case 'price_desc' :
                case 'price_asc' :
                break;
                default :
                    //sortの指定が正しくない
                    return false;
                break;
            }
        }

        //count_per_pageとpage_number
        if ((empty($count_per_page) && !empty($page_number)) || (!empty($count_per_page) && empty($page_number))) {
            //count_per_pageとpage_numberのどちらか一方のみがセットされている
            return false;

        } elseif (!empty($count_per_page) && !empty($page_number)) {
            if (!$this->isNaturalNumber($count_per_page)) {
                //count_per_pageに自然数以外の値が入っている
                return false;
            }
            if (!$this->isNaturalNumber($page_number)) {
                //page_numberに自然数以外の値が入っている
                return false;
            }
        }

        return true;
    }

    //値が自然数かどうかをチェックする
    private function isNaturalNumber($param)
    {
        if (is_numeric($param) && 0 < (int)$param) {
            return true;
        }

        return false;
    }








}