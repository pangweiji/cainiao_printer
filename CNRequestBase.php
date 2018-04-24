<?php

class CNRequestBase
{
    protected $baseUrl    = '';
    protected $sandboxUrl = '';
    public $accessToken   = '';
    public $secrect       = '';
    public $account       = '';
    protected $is_test    = false;

    public function __construct($account)
    {

    }

    public function isTest()
    {
        $this->is_test = true;
    }

    /**
     * 请求接口
     * @param $data
     * @return array
     */
    public function request($apiName, $data=array())
    {
        $param = $this->createRequestParam($apiName, $data);
        $result = $this->Curl($this->url, $param, 'POST');
        if ($result['http_code'] != 200) {
            return false;
        }
        return $result['data'];
    }

    /**
     * 组装请求数据
     * @param string $apiName
     * @param array $data
     * @return array
     */
    public function createRequestParam($apiName, $data)
    {
        $request_data = json_encode($data);
        $signature = $this->sign($request_data);
        $params = [
            'msg_type'              => $apiName,
            'logistic_provider_id'  => $this->accessToken,
            'logistics_interface'   => $request_data,
            'data_digest'           => $signature
        ];
        return $params;
    }

    /**
     * 签名
     * @param string $content
     * @return mixed
     */
    public function sign($content)
    {
        return base64_encode(md5($content.$this->secrect,true));
    }

    public function Curl($url, $vars='', $method = 'GET', $headers = array(), $debug = false){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        //POST OR GET
        if($method != 'GET'){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($vars));
        }else{
            curl_setopt($ch, CURLOPT_POST, 0);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        //SSL
        /*if($this->ssl_verify){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSLVERSION, $this->ssl_version);
        }else{
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }*/

        //DEBUG
        /*if($debug){
            $info   =   curl_getinfo($ch);
            var_dump($info);
            $this->debug_info['http_code']  =   $info['http_code'];
            $this->debug_info['errno']  =   curl_errno($ch);
            $this->debug_info['error']  =   curl_error($ch);
        }*/

        $content = curl_exec($ch);
        $info = curl_getinfo($ch);
        $result['data']      = $content;
        $result['http_code'] = $info['http_code'];
        curl_close($ch);
        return $result;
    }
}