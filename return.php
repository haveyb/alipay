<?php
require __DIR__ . '/vendor/autoload.php';

$type   = $_GET['sign_type'] ?? 'RSA2';
$return = new \haveyb\AliPay\ReturnPage();
header('Location: https://你的前台地址/order/' . ($return->verify($_GET, $type) ? 'success' : 'fail'));
exit;
