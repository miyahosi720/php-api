<?php
/**
 * リクエストやリクエストURLについてのクラス
 */
class Request
{
    /**
     * リクエストメソッドがGETかどうかを判別する
     * @return boolean GETならtrue,それ以外はfalse
     * @author Yoshihiro Yanagawa
     */
    public function isGet()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return true;
        }

        return false;
    }

    /**
     * PATH_INFOを / の区切りごとに分解し配列で返す
     * @param string $path_info
     * @return array PATH_INFOを/の区切りで分解したもの
     * @author Yoshihiro Yanagawa
     */
    public function resolvePathInfo($path_info)
    {
        $uri_segments = array();
        $uri_segments = explode('/', ltrim($path_info, '/'));

        return $uri_segments;
    }

}