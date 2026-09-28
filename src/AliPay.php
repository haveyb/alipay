<?php
declare(strict_types=1);

namespace haveyb\AliPay;

class AliPay extends Base
{
    private $orderInfo;

    public function __construct(string $type = 'RSA2', array $orderInfo = [])
    {
        $this->orderInfo = $orderInfo;
        if ($type === 'MD5') {
            $this->md5Pay();
        } elseif ($type === 'RSA') {
            $this->rsaPay();
        } elseif ($type === 'RSA2') {
            $this->rsa2Pay();
        }
    }

    public function md5Pay(): void
    {
        $params = $this->setSign($this->getOldPayParams($this->orderInfo, 'MD5'), 'MD5');
        header('Location: ' . PAY_GATEWAY . '?' . $this->getUrl($params));
        exit;
    }

    public function rsaPay(): void
    {
        $params = $this->setSign($this->getOldPayParams($this->orderInfo, 'RSA'), 'RSA');
        header('Location: ' . PAY_GATEWAY . '?' . $this->getUrl($params));
        exit;
    }

    public function rsa2Pay(): void
    {
        $info = $this->orderInfo;
        $pubParams = [
            'app_id'      => ALI_PAY_APP_ID,
            'method'      => 'alipay.trade.page.pay',
            'format'      => 'JSON',
            'return_url'  => RETURN_URL,
            'charset'     => 'UTF-8',
            'sign_type'   => 'RSA2',
            'sign'        => '',
            'timestamp'   => date('Y-m-d H:i:s'),
            'version'     => '1.0',
            'notify_url'  => NOTIFY_URL,
            'biz_content' => '',
        ];

        $bizParams = [
            'product_code' => 'FAST_INSTANT_TRADE_PAY',
            'out_trade_no' => $info['order_id'] ?? '',
            'total_amount' => $info['total_fee'] ?? '',
            'subject'      => $info['order_title'] ?? '',
            'body'         => $info['goods_desc'] ?? '',
        ];
        $pubParams['biz_content'] = json_encode($bizParams, JSON_UNESCAPED_UNICODE);

        $pubParams = $this->setSign($pubParams, 'RSA2');
        header('Location: ' . RSA2_PAY_GATEWAY . '?' . $this->getUrl($pubParams));
        exit;
    }

    /**
     * 老接口（MD5 / RSA）请求参数
     */
    private function getOldPayParams(array $orderInfo, string $type = 'MD5'): array
    {
        return [
            'service'        => 'create_direct_pay_by_user',
            'partner'        => ALI_PID,
            'seller_id'      => ALI_PID,
            '_input_charset' => 'UTF-8',
            'sign_type'      => $type,
            'sign'           => '',
            'return_url'     => RETURN_URL,
            'notify_url'     => NOTIFY_URL,
            'payment_type'   => 1,
            'subject'        => $orderInfo['order_title'] ?? '',
            'out_trade_no'   => $orderInfo['order_id'] ?? '',
            'total_fee'      => $orderInfo['total_fee'] ?? '',
            'body'           => $orderInfo['goods_desc'] ?? '',
        ];
    }
}
