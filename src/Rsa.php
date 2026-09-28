<?php
declare(strict_types=1);

namespace haveyb\AliPay;

class Rsa
{
    /**
     * RSA 签名
     * @param string $data        待签名字符串
     * @param string $private_key 应用私钥
     * @param string $type        RSA | RSA2（RSA2 = SHA256WithRSA）
     * @return string base64 签名
     * @throws \RuntimeException 私钥格式错误
     */
    public function rsaSign(string $data, string $private_key, string $type = 'RSA'): string
    {
        $private_key = $this->formatKey($private_key, 'PRIVATE');
        $res = openssl_pkey_get_private($private_key);
        if ($res === false) {
            throw new \RuntimeException('应用私钥格式有误，请检查 APP_PRIVATE_KEY');
        }

        $algo = ($type === 'RSA2') ? OPENSSL_ALGO_SHA256 : OPENSSL_ALGO_SHA1;
        openssl_sign($data, $sign, $res, $algo);
        openssl_free_key($res);

        return base64_encode($sign);
    }

    /**
     * RSA 验签
     * @param string $data       待验签字符串
     * @param string $public_key 支付宝公钥
     * @param string $sign       支付宝返回的 sign（base64）
     * @param string $type       RSA | RSA2
     * @return bool
     * @throws \RuntimeException 公钥格式错误
     */
    public function rsaCheck(string $data, string $public_key, string $sign, string $type = 'RSA'): bool
    {
        $public_key = $this->formatKey($public_key, 'PUBLIC');
        $res = openssl_pkey_get_public($public_key);
        if ($res === false) {
            throw new \RuntimeException('支付宝公钥格式有误，请检查 ALI_RSA2_PUBLIC_KEY');
        }

        $algo = ($type === 'RSA2') ? OPENSSL_ALGO_SHA256 : OPENSSL_ALGO_SHA1;
        $result = (bool) openssl_verify($data, base64_decode($sign), $res, $algo);
        openssl_free_key($res);

        return $result;
    }

    /**
     * 把可能被压缩或换行的密钥还原成标准 PEM 格式
     */
    private function formatKey(string $key, string $kind): string
    {
        $isPrivate = ($kind === 'PRIVATE');
        $begin = $isPrivate ? "-----BEGIN RSA PRIVATE KEY-----" : "-----BEGIN PUBLIC KEY-----";
        $end   = $isPrivate ? "-----END RSA PRIVATE KEY-----" : "-----END PUBLIC KEY-----";

        $key = str_replace(["\r", "\n", "\r\n", $begin, $end], '', $key);
        $key = trim($key);
        return $begin . PHP_EOL . wordwrap($key, 64, "\n", true) . PHP_EOL . $end;
    }
}
