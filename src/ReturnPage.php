<?php
declare(strict_types=1);

namespace haveyb\AliPay;

// 同步返回验签
class ReturnPage extends Base
{
    /**
     * 验证同步返回（return_url）的签名
     */
    public function verify(array $params, string $type = 'RSA2'): bool
    {
        switch ($type) {
            case 'MD5':
                return $this->checkMd5Sign($params);
            case 'RSA':
                return $this->rsaCheck($this->getHandledUrI($params, 'RSA'), ALI_RSA_PUBLIC_KEY, $params['sign'] ?? '', 'RSA');
            case 'RSA2':
                return $this->rsaCheck($this->getHandledUrI($params, 'RSA2'), ALI_RSA2_PUBLIC_KEY, $params['sign'] ?? '', 'RSA2');
            default:
                return false;
        }
    }
}
