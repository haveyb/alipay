<?php
namespace haveyb\AliPay;


class Notify extends Base
{
    public function __construct() {

        // 获取数据
        $postData = $_POST;
        $type = $postData['sign_type'];

        // 验证签名
        switch ($type) {
            case 'MD5' :
                if (!$this->checkMd5Sign($postData)) {
                    $this->logs('log.txt', 'MD5签名失败!');
                    exit();
                } else {
                    $this->logs('log.txt', 'MD5签名成功!');
                }
                 break;
            case 'RSA' :
                if (!$this->rsaCheck($this->getHandledUrI($postData),
                    ALI_RSA_PUBLIC_KEY, $postData['sign']) ) {
                    $this->logs('log.txt', 'RSA签名失败!');
                    exit();
                } else {
                    $this->logs('log.txt', 'RSA签名成功!');
                }
                break;
            case 'RSA2' :
                if (!$this->rsaCheck($this->getHandledUrI($postData),
                    ALI_RSA2_PUBLIC_KEY, $postData['sign'],'RSA2') ) {
                    $this->logs('log.txt', 'RSA2签名失败!');
                    exit();
                } else {
                    $this->logs('log.txt', 'RSA2签名成功!');
                }
                break;
            default :
                break;
        }

        // 验证是否来自支付宝的请求
        if (!$this->isAliPay($postData)) {
            $this->logs('log.txt', '不是来之支付宝的通知!');
            exit();
        } else {
            $this->logs('log.txt', '是来之支付宝的通知验证通过!');
        }

        // 验证交易状态
        if (!$this->checkOrderStatus($postData)) {
             $this->logs('log.txt', '交易未完成!');
             exit();
        } else {
             $this->logs('log.txt', '交易成功!');
        }

        // 验证订单号和金额
        if (false == $this->checkOrderFee($postData)) {
            $this->logs('log.txt', '验证订单金额失败');
            exit();
        } else {
            $this->logs('log.txt', '订单号:' . $postData['out_trade_no'] . '订单金额:' . $postData['total_amount']);
        }

        // 更改订单状态
        if (false == $this->changeOrderStatus($postData)) {
            $this->logs(date('Y-m-d H:i:s'), '更改订单状态失败，订单号为'.$postData['out_trade_no']);
        }

        // 返回success字符串给支付宝 (如果不输出success，支付宝会认为通知没有成功，会不断的发送通知)
        echo 'success';
    }
}

$obj = new Notify();