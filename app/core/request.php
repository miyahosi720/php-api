<?php

class Request
{
    public function isGet()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return true;
        }

        return false;
    }

    /*
     * PATH_INFOを / で分解し配列で返す
     */
    public function resolvePathInfo($path_info)
    {
        $uri_segments = array();
        $uri_segments = explode('/', ltrim($path_info, '/'));
        return $uri_segments;
    }

}