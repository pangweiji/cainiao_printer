<?php
include __DIR__ . '/CNRequestBase.php';
class CNLinkApi extends CNRequestBase
{
    public static $errMsg = '';
    public static $errCode = 0;

    /**
     * 订购关系查询接口
     * @param $cpCode 物流code
     * @return mixed
     */
    public function tmsWaybillSubscriptionQuery($cpCode)
    {
        $msg_type = 'TMS_WAYBILL_SUBSCRIPTION_QUERY';
        $data = array (
            'cpCode' => $cpCode
        );
        $result = $this->request($msg_type, $data);
        if ($result === false) {
            self::$errMsg = '接口请求失败';
            return false;
        }
        $respone_data = json_decode($result, true);
        if (!$respone_data['success']) {
            self::$errMsg = $respone_data['errorMsg'];
            return false;
        }
        return $respone_data['waybillApplySubscriptionCols'];
    }

    /**
     * 电子面单云打印取号
     * @param array $orderInfo 订单信息
     * @param array $shipping 发货渠道信息
     * @return mixed
     */
    public function tmsWaybillGet(Array $orderInfo, Array $shipping)
    {
        $msg_type = 'TMS_WAYBILL_GET';
        $data = $this->getWayBillRequestData($orderInfo, $shipping);
        //$result = $this->request($msg_type, $data);
        //file_put_contents('C:\Users\sw\Desktop\cainiao_tms_way.txt', json_encode($result));
        $result = json_decode(file_get_contents('C:\Users\sw\Desktop\cainiao_tms_way.txt'), true); // 模拟数据
        if ($result === false) {
            self::$errMsg = '接口请求失败：'.$result;
            return false;
        }
        $data = json_decode($result, true);
        if ($data['success'] == false) {
            self::$errMsg = '接口返回错误：'.$data['errorMsg'];
            return false;
        }
        $labelData = $data['waybillCloudPrintResponseList'][0]['printData'];
        $waybillCode = $data['waybillCloudPrintResponseList'][0]['waybillCode'];
        return ['labelData'=>json_decode($labelData,true),'trackNumber'=>$waybillCode];
    }

    /**
     * 设置电子面单请求数据
     * @param array $order
     * @param array $shipping
     */
    private function getWayBillRequestData(Array $order, Array $shipping){
        //发件人信息
        $senderInfo['mobile']   = $order['sender_info']['mobile'];
        $senderInfo['phone']    = $order['sender_info']['phone'];
        $senderInfo['name']     = $order['sender_info']['name'];
        $senderInfo['address']['province'] = $order['sender_info']['province'];
        $senderInfo['address']['district'] = $order['sender_info']['district'];
        $senderInfo['address']['city']     = $order['sender_info']['city'];
        //$senderInfo['address']['town']     = $order['sender_info']['town'];
        $senderInfo['address']['detail']   = $order['sender_info']['address'];

        //面单信息
        $tradeOrderInfoDtos = [];
        //订单信息
        $tradeOrderInfoDto['logisticsServices'] = '';
        $tradeOrderInfoDto['objectId'] = 1;
        $tradeOrderInfoDto['orderInfo'] = [
            'orderChannelsType' => 'OTHERS',
            'tradeOrderList' => [
                $order['order_sn']
            ]
        ];
        $items = [];
        foreach ($order['goods'] as $key => $good){
            $items[$key]['count'] = $good['count'];
            $items[$key]['name'] = $good['name']?$good['name']:$good['goods_name'];
        }
        $tradeOrderInfoDto['packageInfo'] = [
            'id' => $order['pkg_id'],
            'items' => $items,
            'volume' => 1,
            'weight' => 1,
        ];
        $tradeOrderInfoDto['recipient'] = [
            'address' => [
                'city' => $order['receiver_info']['city'],
                'detail' => $order['receiver_info']['address'],
                'district' => $order['receiver_info']['district'],
                'town' => $order['receiver_info']['town'],
                'province' => $order['receiver_info']['province'],
            ],
            'mobile'=>$order['receiver_info']['mobile'],
            'name'=>$order['receiver_info']['name'],
            'phone'=>$order['receiver_info']['phone'],
        ];
        $tradeOrderInfoDto['templateUrl'] = $shipping['cn_url'];
        $tradeOrderInfoDto['userId'] = 12;//userId字段目前无意义，随便传个数字即可
        $tradeOrderInfoDtos[] = $tradeOrderInfoDto;
        //设置主体信息
        $data = [];
        $data['cpCode']         = $shipping['cn_code'];
        $data['dmsSorting']     = false;
        $data['needEncrypt']    = false;
        $data['resourceCode']   = '无';
        $data['storeCode']      = '无';
        $data['tradeOrderInfoDtos'] = $tradeOrderInfoDtos;
        $data['sender']         = $senderInfo;
        return $data;
    }

    /**
     * 电子面单更新接口
     * @param array $order
     * @param Shipping $shipping
     * @return array
     * @throws CytException
     */
    public function tmsWaybillUpdate(Array $order,Shipping $shipping){
        $msg_type = 'TMS_WAYBILL_UPDATE';
        $data   = $this->getWayBillUpdateData($order, $shipping);
        $result = $this->request($msg_type, $data);
        if ($result === false) {
            self::$errMsg = '接口请求失败';
            return false;
        }
        $respone_data = json_decode($result, true);
        if (!$respone_data['success']) {
            self::$errMsg = '接口返回错误信息：'.$respone_data['errorMsg'];
            return false;
        }
        $labelData   = $respone_data['printData'];
        $waybillCode = $respone_data['waybillCode'];
        return ['labelData'=>json_decode($labelData,true),'trackNumber'=>$waybillCode];
    }

    /**
     * 设置电子面单请求更新数据
     * @param array $order
     * @param Shipping $shipping
     */
    private function getWayBillUpdateData(Array $order,Shipping $shipping){
        $items = [];
        $data = [];
        foreach ($order['goods'] as $key => $good){
            $items[$key]['count'] = $good['goods_count'];
            $items[$key]['name'] = $good['goods_spec'];
        }
        $data['cpCode'] = $shipping->cn_code;
        $data['waybillCode'] = $order['tracking_number'];
        $data['objectId'] = 1;
        $data['logisticsServices'] = '';
        $data['packageInfo'] = [
            'items'=>$items,
            'volume'=>1,
            'weight'=>1,
        ];
        $data['sender'] = [
            'mobile'=> $order['sender_info']['mobile'],
            'phone' => $order['sender_info']['phone'],
            'name'  => $order['sender_info']['name'],
        ];
        $data['recipient'] = [
            'address'=>[
                'city'=>$order['receiver_info']['city'],
                'detail'=>$order['receiver_info']['address'],
                'district'=>$order['receiver_info']['town'],
                'province'=>$order['receiver_info']['province'],
                'town'=>'',
            ],
            'mobile'=>$order['receiver_info']['mobile'],
            'name'=>$order['receiver_info']['name'],
            'phone'=>$order['receiver_info']['phone'],
        ];
        $data['templateUrl'] = $shipping->cn_url;
        return $data;
    }

    /**
     * 作废电子面单
     * @param $cpCode
     * @param $waybillCode
     * @return bool
     */
    public function tmsWayBillDiscard($cpCode,$waybillCode){
        $msg_type = 'TMS_WAYBILL_DISCARD';
        $data = array (
            'cpCode' => $cpCode,
            'waybillCode' => $waybillCode
        );
        $result = $this->request($msg_type, $data);
        if ($result === false) {
            self::$errMsg = '接口请求失败';
            return false;
        }
        $respone_data = json_decode($result, true);
        if (!$respone_data['success']) {
            self::$errMsg = '接口返回错误信息：'.$respone_data['errorMsg'];
            return false;
        }
        return $respone_data['discardResult'];
    }
}