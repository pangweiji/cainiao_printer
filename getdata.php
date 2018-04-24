<?php

$result = file_get_contents('C:\Users\sw\Desktop\cainiao_tms_way.txt');

echo $result;



die;

$printData = json_decode($result['printData'], true);
$waybillCode = $result['waybillCode'];
echo '<pre>';
print_r($printData);