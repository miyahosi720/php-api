<?php

class Aucfan_Model
{
    /*
     * DB接続情報
     */
    protected $db_config = array(
        'dsn' => 'mysql:host=localhost;dbname=mydb',
        'user' => 'myuser',
        'password' => 'mypassword'
    );

    //PDOオブジェクト
    protected $dbh;

    public function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        $db_config = $this->db_config;

        try {
            $this->dbh = new PDO($db_config['dsn'], $db_config['user'], $db_config['password']);
        } catch (PDOException $e) {
            var_dump($e->getMessage());
            exit;
        }
    }

    public function execute($sql, $placeholders = array())
    {
        $stmt = $this->dbh->prepare($sql);

        foreach ($placeholders as $key => $value) {
            if (is_numeric($value)) {
                $stmt->bindValue($key, (int)$value, PDO::PARAM_INT); //これをしないと、LIMITのバインドが動作しない
            } else {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }
        }

        $stmt->execute();

        return $stmt;

    }

    public function fetchAll($sql, $placeholders = array())
    {
        return $this->execute($sql, $placeholders)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function __destruct()
    {
        //DB接続解除
        $this->dbh = null;
    }
}