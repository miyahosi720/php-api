<?php
require_once (dirname(__FILE__) . "/../app/models/item.php");

class ItemTest extends PHPUnit_Framework_TestCase
{
    public function testHello()
    {
        $params = array(
            'a' => 1,
            'b' => '2',
        );

        $instance = new Item();

        $method = new ReflectionMethod('Item', 'hello');
        $method->setAccessible(true);

        $this->assertEquals($params, $method->invoke($instance, $params));
    }

    /**
     * GETパラメータが正しい形式の場合
     * @dataProvider validParamProvider
     */
    public function testValidParams($params)
    {
        $method = new ReflectionMethod('Item', 'validateSearchParams');

        $default_params = array(
                'format' => 'json',
                'category_id' => '',
                'price_min' => '',
                'price_max' => '',
                'sort' => '',
                'count_per_page' => '',
                'page_number' => ''
                );

        $expected_params = array_merge($default_params, $params);

        $this->assertEquals($expected_params, $method->invoke((new Item()), $params));
    }

    public function validParamProvider()
    {
        return array(
                array(array('format' => 'json')),
                array(array('category_id' => '1000003')),
                array(array('price_min' => '3000', 'price_max' => '5000')),
                array(array('sort' => '+id')),
                array(array('sort' => '-id')),
                array(array('sort' => '+price')),
                array(array('sort' => '-price')),
                array(array('count_per_page' => '5', 'page_number' => '2'))
            );
    }

    /**
     * 不正な形式のGETパラメータ
     * @dataProvider invalidParamProvider
     */
    public function testInValidParams($params)
    {
        $method = new ReflectionMethod('Item', 'validateSearchParams');

        $this->assertFalse($method->invoke((new Item()), $params));
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

}