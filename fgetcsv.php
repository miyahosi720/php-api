<?php
$csv  = array();
$file = 'item.csv';
$fp   = fopen($file, "r");
 
while (($data = fgetcsv($fp, 0, ",")) !== FALSE) {
  $csv[] = $data;
}
fclose($fp);
 
var_dump($csv);