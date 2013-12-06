<?php

class Uri
{
    /*
     *REQUEST_URIの形式チェックを行うと同時に、action, formatの抽出を行う
     */
    public function extractRequestedActionAndFormat($request_uri)
    {
        // request_uri先頭の/y-api/v1/（10文字）を切り出し
        $base_url = substr($request_uri, 10);

        if ($base_url === false) { 
            //request_uriが/y-api/v1/の場合
            return false;
        } else {

            //urlに?が含まれていたら、?より前の箇所を抜き出す
            if (false !== $pos = strpos($base_url, '?')) {
                $core_url = substr($base_url, 0, $pos);
            } else {
                $core_url = $base_url;
            }
        }

        $pieces = explode(".", $core_url);

        if (count($pieces) != 2) {
            //core_urlに含まれるドットが1つではない、エラー
            return false;
        }

        $action = $pieces[0];
        $format = $pieces[1];

        if ($format != 'json' && $format != 'xml') {
            //.jsonまたは.xml以外の指定、エラー
            return false;
        }

        $requested = array();

        $requested['action'] = $action;
        $requested['format'] = $format;

        return $requested;

    }
}