<?php
return [
    'test' => [ // 阿里提供的测试账号
        'baseUrl'   => 'https://linkdaily.tbsandbox.com/gateway/link.do',
        //'baseUrl'   => 'https://linkdaily.tbsandbox.com/gateway/pac_message_receiver.do', // 都行，不稳定
        
        'appKey' => '',
        'appSecret' => '',

        // 商家token， 有效期为一年，目前是手动授权
        'storeToken' => array(
            'pangweiji' => 'TmpFU1ZOUGoyRnoybDZmT3lyaW9hWGR4VFNad0xNYTBUek9QZk9kamt2Z1hJMytsVkVHK0FjVW55T25wcUR1Qw==',
        ),
    ],

    'product' => [
        'baseUrl'   => 'http://link.cainiao.com/gateway/link.do',
        
        'appKey' => '533966',
        'appSecret' => 'R9Y3A432ZAwPk27K1nA2FA40Q2RlpKmU',

        // 商家token， 有效期为一年，目前是手动授权
        'storeToken' => array(
            'pangweiji' => 'NzhJLzcreU9sdlVWOWxnbmd3MWtWNDV5ek5NTjNDWnFjYTk0aG1lS3VvaTlxUVUxNTlxOUdRUE5mRUhRVnllMg==',
        ),
    ],    
];