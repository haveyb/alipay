<?php
declare(strict_types=1);

namespace haveyb\AliPay;

require_once __DIR__ . '/config.php';

class Base extends Rsa
{
    /**
     * 按支付宝规则处理参数：去掉 sign，RSA 老方式再去掉 sign_type，然后 ksort 拼接
     */
    public function getHandledUrI(array $arr, string $type = 'RSA2'): string
    {
        unset($arr['sign']);
        if ($type === 'RSA') {
            unset($arr['sign_type']);
        }
        ksort($arr);
        return $this->getUrl($arr, false);
    }

    /**
     * 数组转 query 字符串
     * @param bool $encode true=URL 编码（拼最终请求地址用），false=不编码（签名用）
     */
    public function getUrl(array $arr, bool $encode = true): string
    {
        $query = http_build_query($arr);
        return $encode ? $query : urldecode($query);
    }

    public function getSign(array $arr, string $type = 'RSA2'): string
    {
        switch ($type) {
            case 'MD5':
                return md5($this->getHandledUrI($arr, 'MD5') . ALI_MD5_KEY);
            case 'RSA':
                return $this->rsaSign($this->getHandledUrI($arr, 'RSA'), APP_PRIVATE_KEY, 'RSA');
            case 'RSA2':
                return $this->rsaSign($this->getHandledUrI($arr, 'RSA2'), APP_PRIVATE_KEY, 'RSA2');
            default:
                return '';
        }
    }

    public function setSign(array $arr, string $type = 'RSA2'): array
    {
        $arr['sign'] = $this->getSign($arr, $type);
        return $arr;
    }

    public function checkMd5Sign(array $arr): bool
    {
        return $this->getSign($arr, 'MD5') === ($arr['sign'] ?? '');
    }

    /**
     * 老接口（MD5/RSA）的 notify_id 合法性校验。
     * 仅老 mapi 网关有效；RSA2 新接口请用公钥验签，不要再用本方法。
     */
    public function isAliPay(array $arr): bool
    {
        $url = 'https://mapi.alipay.com/gateway.do?service=notify_verify&partner='
            . ALI_PID . '&notify_id=' . ($arr['notify_id'] ?? '');
        $resp = @file_get_contents($url);
        return $resp === 'true';
    }

    public function checkOrderStatus(array $arr): bool
    {
        return in_array($arr['trade_status'] ?? '', ['TRADE_SUCCESS', 'TRADE_FINISHED'], true);
    }

    /**
     * 校验订单金额与订单号是否一致，需对接自己的订单库。
     * 返回 true 才视为有效通知。
     */
    public function checkOrderFee(array $postData): bool
    {
        // 示例：
        // $order = Order::find($postData['out_trade_no']);
        // return $order && $order->amount == $postData['total_amount'];
        return true;
    }

    /**
     * 支付成功后更新订单状态，需对接自己的订单库。
     */
    public function changeOrderStatus(array $postData): bool
    {
        // Order::paySuccess($postData['out_trade_no']);
        return true;
    }

    public function logs(string $filename, $data): void
    {
        if (!is_dir(LOG_PATH)) {
            @mkdir(LOG_PATH, 0755, true);
        }
        file_put_contents(LOG_PATH . $filename, $data . PHP_EOL, FILE_APPEND);
    }
}
