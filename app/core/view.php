<?php

class View
{
    /*
     * ビューファイルを読み込み変数を渡す
     * @param $filepath
     * @param $data
     * 
     */
    public static function render($file, $data)
    {
        foreach ($data as $key => $value) {
            //$dataに入っている変数を設定
            $$key = $value;
        }

        //viewファイルを読み込み
        require('../app/views/' . $file . '.php');

    }
}