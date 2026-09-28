<?php
require __DIR__ . '/vendor/autoload.php';

$notify = new \haveyb\AliPay\Notify();
// 校验通过必须原样输出 success，否则支付宝会持续重发通知
echo $notify->handle() ? 'success' : '';
