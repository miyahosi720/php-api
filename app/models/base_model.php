<?php
/**
 * MySQLに接続しクエリを投げて結果を取得するクラス。
 * @ToDO:クラスの抽象化 
 */

class Base_Model
{
    /*
     * DB接続情報
     */
    protected $db_config = array(
        'dsn' => 'mysql:host=localhost;dbname=apidb',
        'user' => 'root',
        'password' => ''
    );

    //PDOオブジェクト
    protected $dbh;


    protected $memcache;


    public function __construct()
    {
        $this->connect();

        $this->memcache = new Memcache;
        $this->memcache->addServer('localhost', 11211);
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
