# haveyb/alipay

轻量支付宝电脑网站支付封装，内置 MD5 / RSA / RSA2 三种签名方式，便于快速接入，也适合用来学习支付宝支付内部的签名与验签流程。

## 功能特性

1、一行代码发起电脑网站支付，自动拼接参数、签名并跳转收银台。
2、内置 MD5、RSA、RSA2 三种签名与验签实现，RSA2 为当前官方推荐方式。
3、异步通知（notify）与同步返回（return）均提供验签封装，避免裸收回调。
4、零第三方依赖，仅要求 PHP 开启 openssl 扩展。

## 环境要求

- PHP >= 7.1
- 已开启 openssl 扩展
- 电脑网站支付产品已签约（沙箱环境仅支持 RSA2）

## 安装

```bash
composer require haveyb/alipay
```

本地调试执行 `composer install` 生成 `vendor/autoload.php` 即可。

## 配置

打开 `src/config.php`，按需填写以下常量：

| 常量 | 说明 |
| --- | --- |
| `APP_PRIVATE_KEY` | 应用私钥（开放平台「应用私钥」，不是支付宝公钥） |
| `ALI_RSA2_PUBLIC_KEY` | 支付宝公钥（RSA2，与下面 RSA 的不是同一串） |
| `ALI_PAY_APP_ID` | 应用 APPID |
| `RETURN_URL` | 同步跳转地址 |
| `NOTIFY_URL` | 异步通知地址 |
| `IS_DEV` | 是否沙箱：`true` 走沙箱网关，`false` 走正式网关 |
| `ALI_MD5_KEY` / `ALI_RSA_PUBLIC_KEY` / `ALI_PID` | 仅 MD5、RSA 老接口需要 |

应用私钥属于敏感信息，请勿提交进代码仓库；生产环境建议通过环境变量或外部配置注入。

## 快速开始

在订单页引入自动加载，实例化 `AliPay` 并传入订单信息与签名方式，构造函数会直接跳转支付宝收银台：

```php
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
```

## 异步通知

新建 `notify.php` 作为支付宝服务器回调入口：

```php
<?php
require __DIR__ . '/vendor/autoload.php';

$notify = new \haveyb\AliPay\Notify();
// 校验通过必须原样输出 success，否则支付宝会持续重发通知
echo $notify->handle() ? 'success' : '';
```

`Notify::handle()` 内部按顺序完成：验签 →（老接口）来源校验 → 交易状态校验 → 订单金额/订单号校验 → 更新订单状态。其中 `checkOrderFee()` 与 `changeOrderStatus()` 是需要你接入自己订单库的占位方法，默认返回 `true`，生产环境必须实现，否则通知可被伪造。

## 同步返回

用户支付完成后跳回 `return.php`，建议先验签再跳转前端结果页（同步数据可被伪造，只能用于展示，不能据此发货）：

```php
<?php
require __DIR__ . '/vendor/autoload.php';

$type   = $_GET['sign_type'] ?? 'RSA2';
$return = new \haveyb\AliPay\ReturnPage();
header('Location: https://你的前台地址/order/' . ($return->verify($_GET, $type) ? 'success' : 'fail'));
exit;
```

## 三种签名方式说明

- MD5、RSA（老 `mapi` 接口 `create_direct_pay_by_user`）已被支付宝淘汰，仅作学习保留。
- RSA2（`alipay.trade.page.pay`，SHA256WithRSA）是当前标准，沙箱也只支持该方式，生产请优先使用。

切换方式只需修改实例化时的第一个参数：`new \haveyb\AliPay\AliPay('RSA2', $orderInfo);`。

## 安全须知

1、异步通知务必实现 `checkOrderFee()`，比对 `out_trade_no` 与 `total_amount` 与本地订单一致，防止金额被篡改。
2、支付成功状态只能以异步通知为准，同步返回仅用于页面展示。
3、应用私钥不要硬编码进仓库；`logs()` 默认写入 `logs/` 目录，请确保该目录可写。
4、RSA2 验签依赖支付宝公钥（`ALI_RSA2_PUBLIC_KEY`），与 RSA 老接口公钥是不同字符串，需分别填写。

## 目录结构

```
alipay/
├── composer.json
├── index.php        # 发起支付示例
├── notify.php       # 异步通知入口
├── return.php       # 同步跳转入口
├── src/
│   ├── AliPay.php   # 支付入口，拼接参数、签名、跳转
│   ├── Base.php     # 签名/验签/通知校验基类
│   ├── Rsa.php      # RSA/RSA2 签名与验签
│   ├── Notify.php   # 异步通知处理
│   ├── ReturnPage.php # 同步返回验签
│   └── config.php   # 配置项
└── logs/            # 通知日志
```

## License

MIT
