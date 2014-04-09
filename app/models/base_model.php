<?php

require_once (dirname(__FILE__) . "/../core/db.php");
require_once (dirname(__FILE__) . "/../core/memcache.php");

class Base_Model
{
    public function __construct()
    {
        $this->db = new DB();
        $this->memcache = new Mem();
    }

    /**
     * 400エラーの際に出力する内容を返す
     * @return array 出力する内容(エラーコードとメッセージ)
     * @author miyahosi720
     */
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

    /*
     * 値が自然数かどうかをチェックする
     */
    public function isNaturalNumber($string)
    {
        if (is_numeric($string) && 0 < (int)$string) {
            return true;
        }

        return false;
    }

}
