<?php
/**
 * 支付宝配置：复制本文件按需填写下列常量。真实密钥请勿提交进仓库。
 */
namespace haveyb\AliPay;

// 应用私钥（开放平台「应用私钥」，注意不是支付宝公钥）
define('APP_PRIVATE_KEY', '');

// 支付宝公钥（RSA 老方式）
define('ALI_RSA_PUBLIC_KEY', '');

// 支付宝公钥（RSA2 新方式，与上面不是同一串，需分别填写）
define('ALI_RSA2_PUBLIC_KEY', '');

// 应用 APPID
define('ALI_PAY_APP_ID', '');

// 合作者身份 PID（老接口 MD5/RSA 需要）
define('ALI_PID', '');

// 同步通知（支付完成跳转）地址
define('RETURN_URL', 'http://你的域名/alipay/return.php');

// 异步通知地址（支付宝服务器回调）
define('NOTIFY_URL', 'http://你的域名/alipay/notify.php');

// 是否沙箱环境：true=沙箱，false=正式
define('IS_DEV', true);

// MD5 方式密钥（仅 MD5 方式需要）
define('ALI_MD5_KEY', '');

// 日志目录（自动创建，需可写）
define('LOG_PATH', __DIR__ . '/../logs/');

// 老接口网关（MD5 / RSA）
define('PAY_GATEWAY', 'https://mapi.alipay.com/gateway.do');

// 新接口网关（RSA2）
define('RSA2_PAY_GATEWAY',
    IS_DEV ? 'https://openapi.alipaydev.com/gateway.do' : 'https://openapi.alipay.com/gateway.do');
