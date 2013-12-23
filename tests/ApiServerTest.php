<?php
require_once (dirname(__FILE__) . "/../ApiServer.php");

class ApiServerTest extends PHPUnit_Framework_TestCase
{
    public function testhello()
    {
        $params = array(
            'a' => 1,
            'b' => '2',
        );

        $instance = new ApiServer();

        $method = new ReflectionMethod('ApiServer', 'hello');
        $method->setAccessible(true);

        $this->assertEquals($params, $method->invoke($instance, $params));
    }

    /**
     * 不正な形式のパラメータに対しては404のエラーレスポンスが返ることをチェックする
     * @dataProvider invalidParamProvider
     * @runInSeparateProcess
     */
    public function testInvalidParam($param, $format)
    {
        $method = new ReflectionMethod('ApiServer', 'searchItems');

        $expected_error_response_json = '{"error":{"code":"400","message":"Requested parameter is not valid"}}';

        $expected_error_response_xml = '<?xml version="1.0" encoding="UTF-8"?><error><code>400</code><message>Requested parameter is not valid</message></error>';

        //formatにxmlがセットされていたときはxmlで、それ以外はjsonでエラーレスポンスが出力されることを確認
        if ($format == 'xml') {
            $this->assertEquals($expected_error_response_xml, $method->invoke((new ApiServer()), $param, $format));
        } else {
            $this->assertEquals($expected_error_response_json, $method->invoke((new ApiServer()), $param, $format));
        }

        //ステータスコード400チェック
        $this->assertEquals(400, http_response_code());
    }

    public function invalidParamProvider()
    {
        return array(
                array(array('format' => 'aaa'), 'aaa'),
                array(array('format' => '123'), '123'),
                array(array('category_id' => 'aaa'), ''),
                array(array('category_id' => 'aaa'), 'xml'),
                array(array('category_id' => '-123'), ''),
                array(array('category_id' => '-123'), 'xml'),
                array(array('price_min' => 'aaa'), ''),
                array(array('price_min' => 'aaa'), 'xml'),
                array(array('price_min' => '-123'), ''),
                array(array('price_min' => '-123'), 'xml'),
                array(array('price_max' => 'aaa'), ''),
                array(array('price_max' => 'aaa'), 'xml'),
                array(array('price_max' => '-123'), ''),
                array(array('price_max' => '-123'), 'xml'),
                array(array('price_min' => '5000', 'price_max' => '3000'), ''),
                array(array('price_min' => '5000', 'price_max' => '3000'), 'xml'),
                array(array('sort' => 'aaa'), ''),
                array(array('sort' => 'aaa'), 'xml'),
                array(array('sort' => '123'), ''),
                array(array('sort' => '123'), 'xml'),
                array(array('count_per_page' => '5'), ''),
                array(array('count_per_page' => '5'), 'xml'),
                array(array('page_number' => '1'), ''),
                array(array('page_number' => '1'), 'xml'),
                array(array('count_per_page' => '-5', 'page_number' => '1'), ''),
                array(array('count_per_page' => '-5', 'page_number' => '1'), 'xml'),
                array(array('count_per_page' => '5', 'page_number' => '-1'), ''),
                array(array('count_per_page' => '5', 'page_number' => '-1'), 'xml'),
                array(array('count_per_page' => 'aaa', 'page_number' => '1'), ''),
                array(array('count_per_page' => 'aaa', 'page_number' => '1'), 'xml'),
                array(array('count_per_page' => '5', 'page_number' => 'aaa'), ''),
                array(array('count_per_page' => '5', 'page_number' => 'aaa'), 'xml'),
            );
    }

    /**
     * パラメータが正しい形式の場合、商品情報が正しい形式で返ってくることをチェック
     * @dataProvider validParamProvider
     * @runInSeparateProcess
     */
    public function testvalidParam($param, $format)
    {
        $method = new ReflectionMethod('ApiServer', 'searchItems');

        $response = $method->invoke((new ApiServer()), $param, $format);

        if ($format == 'xml') {
            //PHPUnitで'Serialization of 'SimpleXMLElement' is not allowed'が出てテスト出来なかった
        } else {
            $response_array =  json_decode($response, true);
            
            $this->assertEquals(1, count($response_array));
            $this->assertArrayHasKey('result', $response_array);

            $this->assertEquals(3, count($response_array['result']));
            $this->assertArrayHasKey('requested', $response_array['result']);
            $this->assertArrayHasKey('item_count', $response_array['result']);
            $this->assertArrayHasKey('item', $response_array['result']);

            foreach ($response_array['result']['item'] as $item) {
                $this->assertEquals(4, count($item));
                $this->assertArrayHasKey('product_id', $item);
                $this->assertArrayHasKey('category_id', $item);
                $this->assertArrayHasKey('title', $item);
                $this->assertArrayHasKey('price', $item);
            }
        }

    }

    public function validParamProvider()
    {
        return array(
                array(array('format' => 'json'), 'json'),
                array(array('sort' => '+id'), ''),
                array(array('sort' => '-id'), ''),
                array(array('sort' => '+price'), ''),
                array(array('sort' => '-price'), '')
            );
    }

}