<?php
/*
 * Memcacheの処理
 */
class Mem
{
    //Memcacheクラス
    protected $mem;

    public function __construct()
    {
        $this->mem = new Memcache();
        $this->mem->addServer('localhost', 11211);
    }

    /*
     * memcacheからkeyの情報を取得する
     */
    public function get($key)
    {
        return $this->mem->get($key);
    }

    /*
     * memcacheにkey-valueの情報をセットする
     */
    public function set($key, $value)
    {
        $this->mem->set($key, $value);
    }
}