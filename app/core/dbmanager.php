<?php

class DbManager
{
    /*
     * DB接続情報
     */
    protected $params = array(
        'dsn' => 'mysql:host=localhost;dbname=mydb',
        'user' => 'myuser',
        'password' => 'mypassword'
    );

    //PDOオブジェクト
    protected $dbh;

    private function connect()
    {
        $params = $this->params;

        try {
            $this->dbh = new PDO($params['dsn'], $params['user'], $params['password']);
        } catch (PDOException $e) {
            var_dump($e->getMessage());
            exit;
        }
    }

    public function execute($sql, $placeholders = array())
    {
        $this->connect();
        $stmt = $this->dbh->prepare($sql);

        //これをしないと、LIMIT, OFFSETのバインドが動作しない
        foreach ($placeholders as $key => $value) {
            $stmt->bindValue($key, (int)$value, PDO::PARAM_INT);
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