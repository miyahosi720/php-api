<?php

class Format
{

    protected $_data = array();

    public function to_array($data = null)
    {
        return $array;
    }

    public function to_json($data = null)
    {
        return json_encode($data);
    }

    public function to_xml($data = null)
    {
        // return $structure->asXML();
    }


    public function to_php($data = null)
    {
        if ($data === null)
        {
            $data = $this->_data;
        }

        return var_export($data, true);
    }

}
