<?php

class Uri
{
    /*
     * REQUEST_URIからactionとformatを抽出する
     * "/y-api/v1/SearchItems.json" のようなURIであれば、SearchItemsがaction, jsonがformat
     * URIの形式が間違っていればfalseを返す
     */
    public function extractRequestedActionAndFormat($request_uri)
    {
        // request_uri先頭の/y-api/v1/（10文字）は共通なので、切り出す
        $base_uri = substr($request_uri, 10);

        if ($base_uri === false) { 
            //request_uriが/y-api/v1/の場合
            return false;
        } else {

            //urlに?が含まれていたら、?より前の箇所を抜き出す。これをcore_uriと呼ぶ
            // uriが/y-api/v1/SearchItems.json?sort=id_descのとき、core_uriはSearchItems.json
            if (false !== $pos = strpos($base_uri, '?')) {
                $core_uri = substr($base_uri, 0, $pos);
            } else {
                $core_uri = $base_uri;
            }
        }

        //core_uriを.で分割
        $pieces = explode(".", $core_uri);

        if (count($pieces) != 2) {
            //core_uriに含まれるドットが1つではない、エラー
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