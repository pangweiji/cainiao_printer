<?php
include 'D:\phpStudy\WWW\test\lib\vendor\autoload.php';

include __DIR__.'/CNLinkApi.php';

$account = 'huachen';

$cpCode = 'YTO';
//获取 订购关系
$obj = new CNLinkApi($account);
/*$result = $obj->tmsWaybillSubscriptionQuery('YTO');
if (empty($result)) {
    exit('不存在订购关系，请到电子面单后台添加'.PHP_EOL);
}
dump($result);
*/


/***************************** 申请面单 *****************************/
/*$goods = array(
    [
        'name' => '高腰牛仔短裤--深蓝色--2XL',
        'count' => 1
    ]
);
$orderInfo = array(
    'pkg_id' => 1,
    'order_sn'  => '159799174',
    'sender_info' => [
        'mobile'    => '15013740446',
        'phone'     => '15013740446',
        'name'      => '张光余',
        'province'  => '广东省',
        'city'      => '深圳市',
        'district'  => '龙岗区',
        'address'   => '平湖街道平安大道3号乾龙物流园',
    ],
    'goods'     => $goods,
    'receiver_info' => [
        'mobile'    => '13249455626',
        'phone'     => '13249455626',
        'name'      => '二哈',
        'province'  => '广东',
        'city'      => '深圳市',
        'district'  => '龙岗区',
        'town'      => '坂田街道',
        'address'   => '雪岗路2012号',
    ]
);

$cn_url = 'http://cloudprint.cainiao.com/template/standard/101/572?spm=a219a.7629140.0.0.IwoIqJ';
$shipping = array(
    'cn_code' => $cpCode,
    'cn_url' => $cn_url
);
$result = $obj->tmsWaybillGet($orderInfo, $shipping);die;


$result = file_get_contents('C:\Users\sw\Desktop\cainiao_tms_way.txt');

$result = json_decode(json_decode($result, true), true)['waybillCloudPrintResponseList'][0];
$printData = json_decode($result['printData'], true);
$waybillCode = $result['waybillCode'];
echo '<pre>';
print_r($printData);*/


/***************************** 废弃面单 *****************************/
/*$waybillCode = '889190729431347149';
$cpCode = 'YTO';

$result = $obj->tmsWayBillDiscard($cpCode,$waybillCode);
dump($result);*/