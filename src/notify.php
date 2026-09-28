<?php
declare(strict_types=1);

namespace haveyb\AliPay;

// 异步通知处理类：验签、来源校验、状态校验、订单落库
class Notify extends Base
{
    /**
     * 处理支付宝异步通知
     * @return bool 是否验证通过
     */
    public function handle(): bool
    {
        $postData = $_POST;
        $type = $postData['sign_type'] ?? '';

        if (!$this->verifySign($postData, $type)) {
            $this->logs('log.txt', $type . ' 签名验证失败!');
            return false;
        }
        $this->logs('log.txt', $type . ' 签名验证成功!');

        // 老接口额外校验 notify_id；RSA2 公钥验签已足够，不再请求该地址
        if (($type === 'MD5' || $type === 'RSA') && !$this->isAliPay($postData)) {
            $this->logs('log.txt', '不是来自支付宝的通知!');
            return false;
        }
        $this->logs('log.txt', '通知来源验证通过!');

        if (!$this->checkOrderStatus($postData)) {
            $this->logs('log.txt', '交易未完成!');
            return false;
        }
        $this->logs('log.txt', '交易成功!');

        if (!$this->checkOrderFee($postData)) {
            $this->logs('log.txt', '订单金额校验失败!');
            return false;
        }
        $this->logs('log.txt', '订单号:' . ($postData['out_trade_no'] ?? '')
            . ' 金额:' . ($postData['total_amount'] ?? $postData['total_fee'] ?? ''));

        if (!$this->changeOrderStatus($postData)) {
            $this->logs(date('Y-m-d H:i:s'), '订单状态更新失败:' . ($postData['out_trade_no'] ?? ''));
        }

        return true;
    }

    private function verifySign(array $postData, string $type): bool
    {
        switch ($type) {
            case 'MD5':
                return $this->checkMd5Sign($postData);
            case 'RSA':
                return $this->rsaCheck($this->getHandledUrI($postData, 'RSA'), ALI_RSA_PUBLIC_KEY, $postData['sign'] ?? '', 'RSA');
            case 'RSA2':
                return $this->rsaCheck($this->getHandledUrI($postData, 'RSA2'), ALI_RSA2_PUBLIC_KEY, $postData['sign'] ?? '', 'RSA2');
            default:
                return false;
        }
    }
}
