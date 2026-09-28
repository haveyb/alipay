<?php
require __DIR__ . '/vendor/autoload.php';

$orderInfo = [
    'order_title' => '2688元升级大礼包',
    'order_id'    => date('YmdHis') . rand(100000, 999999),
    'total_fee'   => 2688,
    'goods_desc'  => '礼包包含超级经验石100块，助你快速升级',
];

// 沙箱与生产均推荐使用 RSA2
new \haveyb\AliPay\AliPay('RSA2', $orderInfo);
