<?php
require 'vendor/autoload.php';

// 应该从调用出传过来这些信息
$orderInfo = [
    'order_title' => '2688元升级大礼包',
    'order_id' => date('YmdHis').rand(100000, 999999),
    'total_fee' => 2688,
    'goods_desc' => '礼包包含超级经验石100块，助你快速升级'
];

// 实例化AliPay类，并指定方式为RSA2（也可以指定为老版本的md5和RSA，但是沙箱环境只支持RSA2方式）
new \haveyb\AliPay\AliPay('RSA2', $orderInfo);